<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::all();
        return view('admin.pages.banner.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable',
            'sub_title' => 'nullable',
            'description' => 'nullable',
            'link_text' => 'nullable',
            'link' => 'nullable',
        ]);

        try {
            // Get the next sort order
            $maxOrder = Banner::withoutGlobalScope('ordered')->max('sort_order') ?? 0;

            $banner = new Banner();
            $banner->title = $request->title;
            $banner->subtitle = $request->sub_title;
            $banner->description = $request->description;
            $banner->link_text = $request->link_text;
            $banner->link = $request->link;
            $banner->apply_shade = ($request->has('apply_shade')) ? true : false;
            $banner->sort_order = $maxOrder + 1;
            $banner->save();

            if($banner){
                Helper::updateImage($banner, 'banner_image');
                Helper::updateImage($banner, 'mobile_banner_image');
            }

            return response()->json([
                'success' => true,
                'message' => 'Banner created successfully',
            ], 200);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Banner creation failed',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $banner = Banner::find($id);
        $banner->image = Helper::imageDataForFilePond($banner, 'banner_image');
        $banner->mobile_banner_image = Helper::imageDataForFilePond($banner, 'mobile_banner_image');

        $viewpage = view('admin.pages.banner.edit', compact('banner'))->render();

        return response()->json([
            'status' => true,
            'data' => ['data' => $viewpage]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'nullable',
            'sub_title' => 'nullable',
            'description' => 'nullable',
            'link_text' => 'nullable',
            'link' => 'nullable',
        ]);

        try {
            $banner = Banner::find($id);
            $banner->title = $request->title;
            $banner->subtitle = $request->sub_title;
            $banner->description = $request->description;
            $banner->link_text = $request->link_text;
            $banner->link = $request->link;
            $banner->apply_shade = ($request->has('apply_shade')) ? true : false;
            $banner->save();

            if($banner) {
                Helper::updateImage($banner, 'banner_image');
                Helper::updateImage($banner, 'mobile_banner_image');
            }

            return response()->json([
                'success' => true,
                'message' => 'Banner updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating blog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Banner update failed',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        try {
            $banner = Banner::find($id);
            $banner->delete();
            return response()->json([
                'success' => true,
                'message' => 'Banner deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting banner: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Banner deletion failed',
            ], 500);
        }
    }

    public function bannerStatus(Request $request,Banner $banner)
    {
        try {
            if($banner->is_active){
                $banner->is_active = false;
            }else{
                $banner->is_active = true;
            }
            $banner->save();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully.'
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Banner publish failed: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }

    /**
     * Update banner order
     */
    public function updateOrder(Request $request)
    {
        try {
            $orderData = $request->input('order_data');

            if (!$orderData || !is_array($orderData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid order data'
                ], 400);
            }

            foreach ($orderData as $item) {
                if (isset($item['id']) && isset($item['order'])) {
                    Banner::where('id', $item['id'])
                        ->update(['sort_order' => $item['order']]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Banner order updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating banner order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update banner order'
            ], 500);
        }
    }
}
