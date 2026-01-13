<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;
class MediaController extends Controller
{
    public function upload(Request $request)
    {
        if(!env('DEBUGBAR_ENABLED')){
            $collection = $request->header("collection");
            if ($request->header("-method") == "DELETE") {
                $folder = $request->getContent();
                $temporaryFile = FileUpload::where('folder', $folder)->first();
                if ($temporaryFile) {
                    unlink(storage_path('app/tmp/'.$collection.'/' . $folder.'/'.$temporaryFile->file_name));
                    $temporaryFile->delete();
                    rmdir(storage_path('app/tmp/'.$collection.'/' . $folder));
                }
                return "";
            } else {
                if ($request->hasFile($collection)) {
                    $file = $request->file($collection);
                    $fileName = $file->getClientOriginalName();
                    $folder = uniqid() . '-' .now()->timestamp;
                    $file->storeAs('tmp/'.$collection.'/'.$folder, $fileName);
                    FileUpload::create([
                        'file_name' => $fileName,
                        'folder' => $folder,
                    ]);
                    return $folder;
                } else {
                    return "";
                }
            }
        }
    }
}
