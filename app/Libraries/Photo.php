<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/17/18
 * Time: 11:49 AM
 */

namespace App\Libraries;

use Intervention\Image\Facades\Image;

class Photo
{
    protected $thumb = NULL;
    protected $large = NULL;
    protected $file = NULL;
    protected $name = NULL;
    protected $extension = NULL;

    public function __construct($file, array $options = [])
    {
        $this->file = $file;
        $this->name = $file->getClientOriginalName();
        $this->extension = $file->getClientOriginalExtension();
    }
    public function getName()
    {
        return $this->name;
    }

    public function getExtension()
    {
        return $this->extension;
    }

    public function uploadTo($folder, $asName = null)
    {
        if (is_null($asName)) {
            $asName = MD5(microtime()).'.'.$this->extension;
        }
        return $this->file->storeAs('public/' . $folder, $asName);
    }

    public function resizeTo($folder, $width, $height, $asName = null)
    {
        $path = storage_path('app/public/' . $folder);

        if (is_null($asName)) {
            $asName = str_replace($this->extension, '_'.$width. '.'. $this->extension, $this->name);
        }

        Image::make($path)
            ->resize($width, $height, true)
            ->save($path.'/'.$asName, 60);

        return $path . '/' . $asName;

    }
}