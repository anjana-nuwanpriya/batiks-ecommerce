<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inqiry;
use App\Mail\InquiryStatusUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class InquiryController extends Controller
{
    public function index()
    {
        $inquiries = Inqiry::orderBy('created_at', 'desc')->get();
        return view('admin.sales.inquiries.index', compact('inquiries'));
    }


    public function show(Inqiry $inquiry)
    {
        // Log inquiry view if it wasn't read before
        if (!$inquiry->is_read) {
            activity('inquiry_management')
                ->causedBy(auth()->user())
                ->performedOn($inquiry)
                ->withProperties([
                    'operation_type' => 'view',
                    'inquiry_id' => $inquiry->id,
                    'inquiry_subject' => $inquiry->subject ?? 'N/A',
                    'customer_email' => $inquiry->email,
                    'customer_name' => $inquiry->name ?? 'N/A'
                ])
                ->log("Viewed inquiry #{$inquiry->id} from {$inquiry->email}");
        }

        $inquiry->update(['is_read' => 1]);

        $viewpage = view('admin.sales.inquiries.info', compact('inquiry'))->render();

        return response()->json([
            'status' => true,
            'data' => ['data' => $viewpage]
        ]);
    }

    public function update(Request $request, Inqiry $inquiry)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected',
            'admin_notes' => 'nullable|string|max:1000'
        ]);

        $oldStatus = $inquiry->status;
        $oldNotes = $inquiry->admin_notes ?? '';

        // Prepare update data
        $updateData = ['status' => $request->status];

        // Only include admin_notes if the column exists in the database
        if (\Schema::hasColumn('inqiries', 'admin_notes')) {
            $updateData['admin_notes'] = $request->admin_notes;
        }

        // Update inquiry
        $inquiry->update($updateData);

        // Log inquiry update
        $changes = [];
        if ($oldStatus !== $request->status) {
            $changes['status'] = ['from' => $oldStatus, 'to' => $request->status];
        }
        if (isset($updateData['admin_notes']) && $oldNotes !== $request->admin_notes) {
            $changes['admin_notes'] = ['from' => $oldNotes, 'to' => $request->admin_notes];
        }

        if (!empty($changes)) {
            activity('inquiry_management')
                ->causedBy(auth()->user())
                ->performedOn($inquiry)
                ->withProperties([
                    'operation_type' => 'update',
                    'inquiry_id' => $inquiry->id,
                    'customer_email' => $inquiry->email,
                    'customer_name' => $inquiry->name ?? 'N/A',
                    'changes' => $changes,
                    'email_sent' => false // Will be updated below
                ])
                ->log("Updated inquiry #{$inquiry->id} from {$inquiry->email} - " . implode(', ', array_keys($changes)) . " changed");
        }

        // Send email notification to user only if status changed
        if ($request->status !== $oldStatus) {
            try {
                Mail::to($inquiry->email)->send(new InquiryStatusUpdate($inquiry));

                Log::info('Inquiry status email sent successfully', [
                    'inquiry_id' => $inquiry->id,
                    'email' => $inquiry->email,
                    'old_status' => $oldStatus,
                    'new_status' => $request->status
                ]);

                // Log email notification
                activity('inquiry_management')
                    ->causedBy(auth()->user())
                    ->performedOn($inquiry)
                    ->withProperties([
                        'operation_type' => 'email_notification',
                        'inquiry_id' => $inquiry->id,
                        'customer_email' => $inquiry->email,
                        'status_change' => ['from' => $oldStatus, 'to' => $request->status],
                        'notification_type' => 'status_update'
                    ])
                    ->log("Sent status update email to {$inquiry->email} for inquiry #{$inquiry->id}");

                $message = 'Inquiry status updated successfully and notification email sent to customer.';
            } catch (\Exception $e) {
                Log::error('Failed to send inquiry status email: ' . $e->getMessage(), [
                    'inquiry_id' => $inquiry->id,
                    'email' => $inquiry->email,
                    'error' => $e->getMessage()
                ]);

                // Log email failure
                activity('inquiry_management')
                    ->causedBy(auth()->user())
                    ->performedOn($inquiry)
                    ->withProperties([
                        'operation_type' => 'email_notification_failed',
                        'inquiry_id' => $inquiry->id,
                        'customer_email' => $inquiry->email,
                        'error' => $e->getMessage()
                    ])
                    ->log("Failed to send status update email to {$inquiry->email} for inquiry #{$inquiry->id}");

                $message = 'Inquiry status updated successfully, but failed to send notification email.';
            }
        } else {
            $message = 'Inquiry notes updated successfully.';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }


    public function destroy(Inqiry $inquiry)
    {
        // Log inquiry deletion before deleting
        activity('inquiry_management')
            ->causedBy(auth()->user())
            ->performedOn($inquiry)
            ->withProperties([
                'operation_type' => 'delete',
                'inquiry_id' => $inquiry->id,
                'customer_email' => $inquiry->email,
                'customer_name' => $inquiry->name ?? 'N/A',
                'inquiry_subject' => $inquiry->subject ?? 'N/A',
                'inquiry_status' => $inquiry->status,
                'created_at' => $inquiry->created_at->toDateTimeString()
            ])
            ->log("Deleted inquiry #{$inquiry->id} from {$inquiry->email}");

        $inquiry->delete();
        return response()->json([
            'status' => true,
            'message' => 'Inquiry deleted successfully'
        ]);
    }


}
