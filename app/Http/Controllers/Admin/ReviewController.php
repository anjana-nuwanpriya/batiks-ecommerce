<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{

    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('can:manage-reviews');
    }

    public function index()
    {
        $reviews = Review::with(['product', 'user'])->orderBy('created_at', 'desc')->get();
        return view('admin.products.reviews.index', compact('reviews'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function reviewStatus(Review $review)
    {
        try {
            if($review->is_approved){
                $review->is_approved = false;
            }else{
                $review->is_approved = true;
            }
            $review->save();

            return response()->json([
                'status' => true,
                'message' => 'Published successfully.'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Review publish failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showHome(Review $review)
    {
        try {
            if($review->show_in_home){
                $review->show_in_home = false;
            }else{
                $review->show_in_home = true;
            }
            $review->save();

            return response()->json([
                'status' => true,
                'message' => 'Published successfully.'
            ], 201);

        } catch (\Exception $e) {
            Log::error('Review publish failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }


    public function show(Review $review)
    {
        try {
            $review->load(['product', 'user']);
            $page = view('admin.products.reviews.view', compact('review'))->render();
            return response()->json([
                'status' => true,
                'data' => $page
            ], 200);

        }catch (\Exception $e) {
            Log::error('Review show failed: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
