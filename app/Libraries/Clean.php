<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/17/18
 * Time: 11:49 AM
 */

namespace App\Libraries;


use Illuminate\Support\Facades\File;

class Clean
{
    public static function pano($directory)
    {
        foreach(glob($directory . '/{*.kro}', GLOB_BRACE) as $filePath)
        {
            File::delete($filePath);
        }

        File::deleteDirectory($directory . '/images');
    }

    public static function video($directory)
    {
        foreach(glob($directory . '/{*.jpg}', GLOB_BRACE) as $filePath)
        {
            File::delete($filePath);
        }

        File::deleteDirectory($directory . '/tablet');
    }
}