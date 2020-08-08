<?php

/**
 * 文件处理模块基类
 * 文件保存、修改、获取等相关的逻辑
 * 涉及到文件存储方式
 */

namespace App\Libraries\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Http\File as FileObject;

use Storage;
use File;

abstract class StorageBase
{

    /**
     * 存储引擎
     * @var string
     */
    protected $disk = 'static';

    /**
     * 前缀信息
     * @var string
     */
    protected $prefix = '';

    /**
     * 保存上传文件
     * @author leesenlen
     * @date   2018-08-15
     */
    public function saveUpload($file)
    {
        // 默认存储到本地
        $localPath = $this->getLocalPath();
        $filepath = Storage::disk($this->disk)->putFile($localPath, $file);

        return $filepath;
    }

    /**
     * 保存网络文件
     * @author leesenlen
     * @date   2018-08-16
     */
    public function saveRemoteFile($url)
    {
        $url      = explode('?',$url)[0];
        $filename = basename($url);
        $localPath = $this->getLocalPath();
        $filepath = $localPath .'/'. $filename;

        $handle = fopen($url, 'r');
        Storage::put($filepath, $handle);
        @fclose($handle);

        return $filepath;
    }

    public function saveFileFromLocal($path)
    {
        $localPath = $this->getLocalPath();
        Storage::disk($this->disk)->put($path, Storage::get($path));
        return $path;
    }

    /**
     * 获取本地文件路径
     * @author leesenlen
     * @date   2018-08-16
     */
    public function getLocalPath()
    {
        $path = ($this->prefix ? $this->prefix . "/" : '') . date('Y') . '/' . date('m') . '/' . date('d') ;
        return $path;
    }

    /**
     * 生成路径前缀
     * @author leesenlen
     * @date   2018-08-16
     */
    public function setPrefix($prefix = '')
    {
        $this->prefix = $prefix;
    }


    /**
     * 获取文件链接
     * @author leesenlen
     * @date   2018-08-15
     */
    public function getFileLink($path='')
    {
        $config = $this->getDriverConfig();
        if (!empty($config['link'])) {
            $url = "https://" . $config['link'] . "/" . (!empty($config['root']) ? ($config['root'] . "/") : "") .  trim($path, "/");
        } else {
            $url = Storage::disk($this->disk)->url(trim($path, "/"));
        }
        return $url;
    }

    public function getDriverConfig()
    {
        return config('filesystems.disks.' . $this->disk);
    }

    /**
     * 删除文件
     */
    public function deleteFile($path='')
    {
        Storage::disk($this->disk)->delete($path);
    }
}