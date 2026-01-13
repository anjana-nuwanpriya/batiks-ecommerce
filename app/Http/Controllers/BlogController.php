<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blogs = Blog::all();
        return view('admin.blogs.index', compact('blogs'));
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
            'title' => 'required',
            'content' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
        ]);

        try {
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->slug = str()->slug($request->title);
            $blog->content = $request->content;
            $blog->meta_title = $request->meta_title;
            $blog->meta_description = $request->meta_description;
            $blog->save();

            if($blog) {
                Helper::updateImage($blog, 'blog_thumbnail');
            }

            return response()->json([
                'success' => true,
                'message' => 'Blog created successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating blog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Blog creation failed',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        $blog->blog_thumb = Helper::imageDataForFilePond($blog, 'blog_thumbnail');

        $viewpage = view('admin.blogs.edit', compact('blog'))->render();

        return response()->json([
            'status' => true,
            'data' => ['data' => $viewpage]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
            'meta_title' => 'nullable',
            'meta_description' => 'nullable',
        ]);

        try {
            $blog->title = $request->title;
            $blog->slug = str()->slug($request->title);
            $blog->content = $request->content;
            $blog->meta_title = $request->meta_title;
            $blog->meta_description = $request->meta_description;
            $blog->save();

            if($blog) {
                Helper::updateImage($blog, 'blog_thumbnail');
            }

            return response()->json([
                'success' => true,
                'message' => 'Blog updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating blog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Blog update failed',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        try {
            $blog->delete();
            return response()->json([
                'success' => true,
                'message' => 'Blog deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting blog: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Blog deletion failed',
            ], 500);
        }
    }

    public function blogStatus(Request $request,Blog $blog)
    {
        try {
            if($blog->is_published){
                $blog->is_published = false;
            }else{
                $blog->is_published = true;
            }
            $blog->save();

            return response()->json([
                'status' => true,
                'message' => 'Updated successfully.'
            ], 200);

        } catch (\Throwable $th) {
            Log::error('Blog publish failed: ' . $th->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'An error occurred',
            ], 500);
        }
    }
}
