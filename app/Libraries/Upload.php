<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/17/18
 * Time: 11:49 AM
 */

namespace App\Libraries;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Upload
{
    public static function toS3($pathFile, $pathTo, $delete = false, $options = [])
    {
        $options['visibility'] = 'public';
        if(empty(pathinfo($pathTo, PATHINFO_EXTENSION))) {
            $file = Storage::disk('s3')->putFile($pathTo, new \Illuminate\Http\File($pathFile), $options);
        } else {
            $file = Storage::disk('s3')->putFileAs(dirname($pathTo), new \Illuminate\Http\File($pathFile), basename($pathTo), $options);
        }

        if ($delete) {
            \Illuminate\Support\Facades\File::delete($pathFile);
        }
        return Storage::disk('s3')->url($file);
    }
    /**
     * $to: scenes/{id}
    */
    public static function movePanoToS3($directory, $to)
    {
        $result = [
            'total' => 0,
            'xml' => '',
            'thumb' => ''
        ];
        $total = 0;
        //get xml
        foreach(glob($directory . '/{*.xml}', GLOB_BRACE) as $filePath)
        {
            $s3Path = substr($filePath, strpos($filePath, $to)) ;
            $result['xml'] = self::toS3($filePath, $s3Path, true, [
                'CacheControl' => 'no-cache, no-store, must-revalidate',
                'Expires' => 0
            ]);
            $total++;
        }

        //get preview
        $filePath = $directory . '/images/preview.jpg';
        $s3Path = substr($filePath, strpos($filePath, $to)) ;
        self::toS3($filePath, $s3Path, true);
        $total++;

        //get thumb
        $filePath = $directory . '/images/thumb.jpg';
        $s3Path = substr($filePath, strpos($filePath, $to)) ;
        $result['thumb'] = self::toS3($filePath, $s3Path, true);
        $total++;

        //get images
        foreach(glob($directory . "/images/*/*/*/{*.jpg}", GLOB_BRACE) as $filePath)
        {
            $files[] = $filePath;
            $s3Path = substr($filePath, strpos($filePath, $to)) ;
            self::toS3($filePath, $s3Path, true);
            $total++;
        }
        $result['total'] = $total;

        return $result;
    }

    public static function moveVideoToS3($directory, $to)
    {
        $result = [
            'total' => 0,
            'data' => [
                'tablet' => [],
            ],
            'preview' => ''
        ];
        $total = 0;

        foreach(glob($directory . '/{*.jpg}', GLOB_BRACE) as $filePath)
        {
            $s3Path = substr($filePath, strpos($filePath, $to)) ;
            $result['preview'] = self::toS3($filePath, $s3Path, true, [
                'CacheControl' => 'no-cache, no-store, must-revalidate',
                'Expires' => 0
            ]);
            $total++;
        }

        foreach(glob($directory . '/tablet/{*.webm}', GLOB_BRACE) as $filePath)
        {
            $s3Path = substr($filePath, strpos($filePath, $to)) ;
            $result['data']['tablet']['webm'] = self::toS3($filePath, $s3Path, true);
            $total++;
        }

        foreach(glob($directory . '/tablet/{*.mp4}', GLOB_BRACE) as $filePath)
        {
            $s3Path = substr($filePath, strpos($filePath, $to)) ;
            $result['data']['tablet']['mp4'] = self::toS3($filePath, $s3Path, true);
            $total++;
        }

        foreach(glob($directory . '/desktop/{*.webm}', GLOB_BRACE) as $filePath)
        {
            $s3Path = substr($filePath, strpos($filePath, $to)) ;
            $result['data']['desktop']['webm'] = self::toS3($filePath, $s3Path, true);
            $total++;
        }

        foreach(glob($directory . '/desktop/{*.mp4}', GLOB_BRACE) as $filePath)
        {
            $s3Path = substr($filePath, strpos($filePath, $to)) ;
            $result['data']['desktop']['mp4'] = self::toS3($filePath, $s3Path, true);
            $total++;
        }

        $result['total'] = $total;

        return $result;
    }

    public static function uploadImage($directory, $file, $options = [], $fileName = null)
    {
        $ext = $file->guessExtension();
        $size = $file->getClientSize();
        if (is_null($fileName)) {
            $fileName = time();
        }
        $fileName .= '.' . $ext;
        if (empty($options) || !key_exists('disk', $options)) {
            $options['disk'] = 's3';
        }
        $file = $file->storePubliclyAs($directory, $fileName, $options);
        $result = [
            'img' => Storage::disk('s3')->url($file),
            'size' => $size
        ];

        return $result;
    }
}