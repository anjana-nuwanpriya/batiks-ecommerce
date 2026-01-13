<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\FlashDeal;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlashDealController extends Controller
{
    public function index()
    {
        $flashDeals = FlashDeal::all();
        return view('admin.flash-deals.index', compact('flashDeals'));
    }

    public function create()
    {
        $products = Product::where('is_active', 1)->get();
        return view('admin.flash-deals.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_range' => 'required|string',
            'products' => 'required|array',
        ]);

        try {
            DB::beginTransaction();
            $dateRange = explode(' to ', $request->date_range); //2025-06-11 to 2025-06-14
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $flashDeal = FlashDeal::create([
                'title' => $request->title,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $request->status ?? 0,
            ]);

            if($flashDeal){

                Helper::updateImage($flashDeal, 'flash_deal_banner');

                foreach ($request->selected_products as $productId) {
                    $flashDeal->items()->create([
                        'flash_deal_id' => $flashDeal->id,
                        'product_id' => $productId,
                        'quantity' => $request->qty[$productId],
                        'discount_type' => $request->discount_type[$productId],
                        'discount' => $request->discount[$productId],
                        'status' => $request->status ?? 0,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Flash deal created successfully',
                'data' => $flashDeal
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating flash deal: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }

    }

    public function edit(FlashDeal $flashDeal)
    {
        $products = Product::where('is_active', 1)->get();
        $flashDeal->banner = Helper::imageDataForFilePond($flashDeal, 'flash_deal_banner');
        return view('admin.flash-deals.edit', compact('flashDeal', 'products'));
    }

    public function update(Request $request, FlashDeal $flashDeal)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date_range' => 'required|string',
            'products' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $dateRange = explode(' to ', $request->date_range); //2025-06-11 to 2025-06-14
            $startDate = $dateRange[0];
            $endDate = $dateRange[1];

            $flashDeal->update([
                'title' => $request->title,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $request->status,
            ]);

            $flashDeal->items()->delete();

            foreach ($request->selected_products as $productId) {
                $flashDeal->items()->create([
                    'flash_deal_id' => $flashDeal->id,
                    'product_id' => $productId,
                    'quantity' => $request->qty[$productId],
                    'discount_type' => $request->discount_type[$productId],
                    'discount' => $request->discount[$productId],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Flash deal updated successfully',
                'data' => $flashDeal
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating flash deal: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }



    public function flashDealStatus(FlashDeal $flashDeal)
    {
        try {
            if($flashDeal->status){
                $flashDeal->status = false;
            }else{
                $flashDeal->status = true;
            }
            $flashDeal->save();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully.'
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Flash deal publish failed: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }


}
