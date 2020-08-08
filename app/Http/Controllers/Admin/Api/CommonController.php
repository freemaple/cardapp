<?php
namespace App\Http\Controllers\Admin\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Excel;

class CommonController extends Controller
{

     /**
     * base64
     *
     * @return void
    */
    public function image2base64(Request $request)
    {
        $result = [];

        //检查文件上传
        $file = $request->file('image');
        if(!$file || !$file->isValid()){
            $result['code'] = '0x0x0f';
            $result['message'] = 'This is not a valid image.';
            return json_encode($result);
        }

        $path = $file->path();

        $str = file_get_contents($path);

        $type = $file->getClientMimeType();

        $str = "data:".$type.";base64,".base64_encode($str);
        
        $result['code'] = '200';
        $result['message'] = 'Success.';
        $result['imgsrc'] = $str;

        return json_encode($result);
    }
}