<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{

    public function homePage()
    {
        return view('admin.pages.home_page');
    }

    public function aboutPage()
    {
        $sections = AboutPage::all();
        return view('admin.pages.about', compact('sections'));
    }

    public function updateAboutSection(Request $request, $sectionName)
    {
        $request->validate([
            'content' => 'required|array',
            // 'is_active' => 'boolean'
        ]);

        // Handle file uploads
        $content = $request->content;
        foreach ($content as $key => $value) {
            if ($request->hasFile("content.{$key}")) {
                $file = $request->file("content.{$key}");
                $path = $file->store('about', 'public');
                $content[$key] = $path;
            }
        }

        AboutPage::updateOrCreate(
            ['section_name' => $sectionName],
            [
                'content' => $content,
                'is_active' => $request->boolean('is_active', true)
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Section updated successfully'
        ]);
    }

    public function getAboutSection($sectionName)
    {
        $section = AboutPage::getSection($sectionName);
        return response()->json($section);
    }

}
