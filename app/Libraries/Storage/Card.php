<?php

/**
 * 用户相关图片处理
 */

namespace App\Libraries\Storage;

use App\Models\Auth\UserAuth;

class Card extends StorageBase
{

    /**
     * 定义
     * @var string
     */

    protected $prefix = '';

    /**
     * 用户实例
     * @var null
     */
    protected $model = null;

    public function __construct($type = '', UserAuth $user=null)
    {
        $this->prefix = $type;
        $this->model = $user;
    }

    public function getLocalPath()
    {
        // $this->model custom
        $path = $this->prefix ? $this->prefix . "/"  : ''; # TODO

        return $path;
    }


}