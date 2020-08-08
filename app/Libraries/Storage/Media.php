<?php

/**
 * 产品相关文件处理
 * 橱窗图、详情图、缩略图等
 */

namespace App\Libraries\Storage;
use Storage;
use Illuminate\Http\File;

class Media extends StorageBase
{

    /**
     * 定义
     * @var string
     */
    protected $disk = 'media';

    protected $prefix = '';


    public function saveFile($dir,$filePath,$fileName)
    {
        return Storage::disk($this->disk)->putFileAs($dir, new File($filePath), $fileName); 
    }

}