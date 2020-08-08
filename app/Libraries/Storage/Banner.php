<?php

/**
 * banner图片处理
 */

namespace App\Libraries\Storage;

class Banner extends StorageBase
{

    /**
     * 定义
     * @var string
     */

    protected $prefix = 'banner';

    public function getLocalPath()
    {
        // $this->model custom
        $path = $this->prefix ? $this->prefix . "/" : ''; # TODO

        $path = rtrim($path, '/');
        return $path;
    }


}