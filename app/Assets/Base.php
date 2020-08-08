<?php
namespace App\Assets;
use App;

class Base
{

    //action
    protected static $action = '';

    //controller
    protected static $controller = '';

    /**
     *  根据media配置文件，加载对应Controller的css和js
    */
    private static function getAction()
    {
        if(static::$action === ''){
            $route_current = \Route::current();
            //判断当前路由是否为空和getActionName方法
            if(!empty($route_current) && method_exists($route_current, 'getActionName')){
                //获取当前控制器名称
                $action_name = $route_current->getActionName(); 
                list($controller, $action) = explode('@', $action_name); 
                static::$action = $action;
                static::$controller = str_replace("App\Http\Controllers\\", '', $controller);
            } else {
                static::$action = null;
                static::$controller = null;
            }
        } 
    }

    /**
     * 获取根路径
     */

    public static function basePath(){
        return static::$base_path;
    }

    /**
     *  根据media配置文件，加载对应Controller的css和js
    */
    public static function styles($version = '')
    {

        $site = static::$site;

        if(empty($site)){
            return null;
        }

        $media = config($site . '_media');
        $styles = array();

        //获取站点配置
        $site_config = config($site);
        if($version == null){
            //版本号
            $version = isset($site_config['version']) ? $site_config['version'] : ''; 
        }
        $base_path = static::basePath();

        if(isset($media['default']['styles'])){
            //公共样式
            foreach ($media['default']['styles'] as $style)
            {
                $style_path = $base_path . $style. '?' . $version;
                if(!in_array($style_path, $styles)){
                    $styles[] = $style_path;
                }
            }
        }
    
        static::getAction();

        $controller = static::$controller;

        $action = static::$action;

        //Controller css
        if(array_key_exists($controller, $media) and isset($media[$controller]['styles']))
        {
            foreach ($media[$controller]['styles'] as $style) 
            {
                $style_path = $base_path . $style. '?' . $version;
                if(!in_array($style_path, $styles)){
                    $styles[] = $style_path;
                }
            }
        }
        if(!empty($controller) && !empty($action)){
            $action = $controller. "@" . $action; 
            //action css
            if(array_key_exists($action, $media) and isset($media[$action]['styles'])){
                foreach ($media[$action]['styles'] as $style) {
                    $style_path = $base_path . $style. '?' . $version;
                    if(!in_array($style_path, $styles)){
                        $styles[] = $style_path;
                    }
                }
            }
        }

        return $styles;
    }

    /**
     *  加载css
    */
    public static function scripts($version = '')
    {
        $site = static::$site;

        if(empty($site)){
            return null;
        }
        $media = config($site . "_media");
        $scripts = array();

        //获取站点配置
        $site_config = config($site);
        if($version == null){
            //版本号
            $version = isset($site_config['version']) ? $site_config['version'] : '';
        }
        $base_path = static::basePath();

        if(isset($media['default']['scripts'])){
            //公共js
            foreach ($media['default']['scripts'] as $script){
                $script_path = $base_path.$script. '?' . $version;
                if(!in_array($script_path, $scripts)){
                    $scripts[] = $script_path;
                }
            }
        }
    
        static::getAction();

        $controller = static::$controller;

        $action = static::$action;

        //Controller JS
        if(array_key_exists($controller, $media) and isset($media[$controller]['scripts']))
        {
            foreach ($media[$controller]['scripts'] as $script) {
                $script_path = $base_path.$script. '?' . $version;
                if(!in_array($script_path, $scripts)){
                    $scripts[] = $script_path;
                }
            }
        }
        if(!empty($controller) && !empty($action)){
            $action = $controller. "@" . $action; 
            //action JS
            if(array_key_exists($action, $media) and isset($media[$action]['scripts'])){
                foreach ($media[$action]['scripts'] as $script) {
                    $script_path = $base_path.$script. '?' . $version;
                    if(!in_array($script_path, $scripts)){
                        $scripts[] = $script_path;
                    }
                }
            }
        }
        return $scripts;
    }

    /**
     * 生成script
     * @param  string $file    文件路径
     * @param  string $version 版本号
     * @return string
     */
    public static function script($file = null, $version = null){
        if($file == null){
            return  null;
        }
        $base_path = static::basePath();
        return sprintf('<script type="text/javascript" src="%s%s?%s"></script>', $base_path, $file, $version);
    }

    /**
     * 生成style
     * @param  string $file    文件路径
     * @param  string $version 版本号
     * @return string
     */
    public static function style($file = null, $version = null){
        if($file == null){
            return  null;
        }
        $base_path = static::basePath();
        return sprintf('<link type="text/css" href="%s%s?%s" rel="stylesheet" />', $base_path, $file, $version);
    }
}
