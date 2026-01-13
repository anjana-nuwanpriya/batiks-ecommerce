<?php

namespace App\Helpers;
use App\Models\FileUpload;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class Helper
{
    /**
     * Generate a slug from a given string
     *
     * @param string $string
     * @return string
     */
    public static function makeSlug($string)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }

    /**
     * Format file size
     *
     * @param int $size
     * @return string
     */
    public static function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * Truncate text to a specified length
     *
     * @param string $text
     * @param int $length
     * @param string $ending
     * @return string
     */
    public static function truncateText($text, $length = 100, $ending = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $ending;
    }

    /**
     * Check if string is valid JSON
     *
     * @param string $string
     * @return bool
     */
    public static function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Update image
     *
     * @param Model $model
     * @param string $collection
     * @return void
     */
    public static function updateImage($model, $collection) {

        if (!empty(request($collection))) {
            $request_item = request($collection);
            if(is_array($request_item)){
                if(request()->has($collection.'_deleted') && request($collection.'_deleted') == 1){
                    foreach (explode(',', request($collection.'_deleted_ids')) as $item_id) {
                        self::destroyImageHelper($model,$collection,$item_id);
                    }
                }
                foreach ($request_item as $item) {
                    self::storeImageHelper($model,$collection,$item);
                }
            }else{
                if(request()->has($collection.'_deleted') && request($collection.'_deleted') == 1){
                    $media = $model->getFirstMedia($collection);
                    if(!empty($media)){
                        self::destroyImageHelper($model,$collection,$media->id);
                    }
                }
                self::storeImageHelper($model,$collection,$request_item);
            }
        }
    }

    /**
     * Store image helper
     *
     * @param Model $model
     * @param string $collection_name
     * @param string $tem_file_id
     * @return void
     */
    public static function storeImageHelper($model, $collection_name, $tem_file_id, $watermark=false, $webp_copy=false, $quality_optimize=false) {
        $temporaryFile = FileUpload::where('folder', $tem_file_id)->first();
        if ($temporaryFile) {
            $image = Image::load(storage_path('app/tmp/'.$collection_name.'/'.$tem_file_id.'/'.$temporaryFile->file_name));

            if($watermark){
                $watermark_image = storage_path('app/public/logo/watermark.png');
                $image->watermark($watermark_image)->watermarkPosition(Manipulations::POSITION_CENTER)->save();
            }

            $manipulations = [];
            // $manipulations = [
            //     '*' => ['orientation' => '90'],
            // ];

            $media = $model->addMedia(storage_path('app/tmp/'.$collection_name.'/'.$tem_file_id.'/'.$temporaryFile->file_name))->withManipulations($manipulations)->toMediaCollection($collection_name);

            // $model->addMedia(storage_path('app/tmp/'.$collection_name.'/'.$tem_file_id.'/'.$temporaryFile->file_name))->withManipulations([
            //     'thumb' => ['orientation' => '90'],
            // ])->toMediaCollection($collection_name);

            $image = Image::load(storage_path('app/public/'.$media->id.'/'.$media->file_name));

            if($webp_copy){
                $image->format(Manipulations::FORMAT_WEBP)->save(storage_path('app/public/'.$media->id.'/'.$media->file_name).'.webp');
            }

            if($quality_optimize){
                $image->quality(60)->save();
            }

            rmdir(storage_path('app/tmp/' . $collection_name . '/' . $tem_file_id));
            $temporaryFile->delete();
        }
    }


    /**
     * Destroy image helper
     *
     * @param Model $model
     * @param string $collection_name
     * @param string $tem_id
     * @return void
     */
    public static function destroyImageHelper($model, $collection_name, $tem_id) {
        $media = $model->getMedia($collection_name)->where('id', $tem_id)->first();
        if(!empty($media)){
            $model->deleteMedia($media);
        }
    }

    /**
     * Image data for file pond
     *
     * @param Model $model
     * @param string $collection
     * @return Collection
     */
    public static function imageDataForFilePond($model, $collection) {
        $media_details = array();
        $medias = $model->getMedia($collection);
        foreach ($medias as $media){
            $detail = [
                'source' => $media->getFullUrl(),
                'options' => [
                    'type' => 'local',
                    'file' => [
                        'name' => $media->file_name,
                        'size' => $media->size,
                        'type' => $media->mime_type,
                    ],
                    'metadata' => [
                        'poster' => $media->getFullUrl(),
                    ],
                ]
            ];
            array_push($media_details, $detail);
        }
        return collect($media_details);
    }

    /**
     * Move image helper
     *
     * @param Model $from_model
     * @param string $collection_name
     * @param string $item_id
     * @param Model $to_model
     */
    public static function moveImageHelper($from_model, $collection_name,$item_id, $to_model) {
        $media = $from_model->getMedia($collection_name)->where('id', $item_id)->first();
        if(!empty($media)){
            // $model->deleteMedia($media);
            $media->move($to_model, $collection_name);
        }
    }

}