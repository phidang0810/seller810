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
    protected $pathFile = NULL;

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
        $this->pathFile = $this->file->storeAs('public/' . $folder, $asName);
        return $this->pathFile;
    }

    public function resizeTo($width, $asName = null)
    {
        $pathFile = storage_path('app/' . $this->pathFile);
        if (is_null($asName)) {
            $asName = str_replace('.' . $this->extension, '_'.$width. '.'. $this->extension, $this->name);
        }

        $pathDirectory = dirname($pathFile);

        $exportTo = $pathDirectory.'/'.$asName;

        Image::make($pathFile)
            ->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            })
            ->save($exportTo, 60);

        return dirname($this->pathFile) . '/' . $asName;

    }
}