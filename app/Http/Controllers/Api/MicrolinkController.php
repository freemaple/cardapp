<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\CardService;
use Auth;
use App\Models\Microlink\Microlink;
use App\Libs\Service\MicrolinkService;
USE App\Models\Card\CardMicrolink;


class MicrolinkController extends BaseController
{
    /**
     * 添加
     * @param  Request $request 
     * @return string           
     */
    public function load(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required',
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "Invalid_Parameter";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $id = $request->id;
        $user = Auth::user();
        $user_id = $user->id;
        $microlink = MicrolinkService::find($id);
        if($microlink != null){
            if($microlink['user_id'] != $user_id){
                $result['code'] = "no_exits";
                $result['message'] = '微链接不存在！';
                return response()->json($result);
            }
            $icon = $microlink->icon()->first();
            $microlink = $microlink->toArray();
            if($icon != null){
                $microlink['svg'] = $icon['svg'];
            }
        }
        $result['data'] = $microlink;
        $result['code'] = "Success";
        $result['message'] = 'Success';
        return response()->json($result);
    }
	
    /**
     * 添加
     * @param  Request $request 
     * @return string           
     */
    public function add(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'icon_id' => 'int|min:1',
            'card_id' => 'required|int|min:1',
            'link' => 'required|url'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "Invalid_Parameter";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $name = $request->name;
        if($name != ''){
            if(!preg_match('/^[.,，A-Za-z0-9_\s\x{4e00}-\x{9fa5}]+$/u', $name)){
                $result['code'] = "Invalid_Parameter";
                $result['message'] = '名称只能包含中文,英文字母和数字及上下划线,逗号！';
                return $result;
            }
        }
    	$user = Auth::user();
        $microlink = new Microlink();
        $microlink->user_id = $user->id;
        $microlink->name = trim($request->name);
        $microlink->icon_id = trim($request->icon_id);
        $microlink->link = trim($request->link);
        $microlink->save();
        $CardMicrolink = new CardMicrolink();
        $card_id = $request->card_id;
        $CardMicrolink->card_id = $card_id;
        $CardMicrolink->user_id = $user->id;
        $CardMicrolink->microlink_id = $microlink->id;
        $CardMicrolink->save();
        $result['code'] = "Success";
        $result['message'] = '保存成功';
    	return response()->json($result);
    }

    /**
     * 编辑
     * @param  Request $request 
     * @return string           
     */
    public function edit(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'icon_id' => 'int|min:1',
            'link' => 'required|url|max:1000'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "Invalid_Parameter";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $name = $request->name;
        if($name != ''){
            if(!preg_match('/^[.,，A-Za-z0-9_\s\x{4e00}-\x{9fa5}]+$/u', $name)){
                $result['code'] = "Invalid_Parameter";
                $result['message'] = '名称只能包含中文,英文字母和数字及上下划线,逗号！';
                return $result;
            }
        }
        $user = Auth::user();
        $id = $request->id;
        $microlink = Microlink::where('id', $id)->where('user_id', '=', $user->id)->first();
        if($microlink == null){
            $result['code'] = "microlink_not_exist";
            $result['message'] = '微链接不存在!';
            return response()->json($result);
        }
        $microlink->user_id = $user->id;
        $microlink->name = trim($request->name);
        $microlink->icon_id = trim($request->icon_id);
        $microlink->link = trim($request->link);
        $microlink->save();
        $result['code'] = "Success";
        $result['message'] = '保存成功';
        return response()->json($result);
    }

    /**
     * 删除
     * @param  Request $request 
     * @return string           
     */
    public function remove(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'id' => 'required',
            'icon' => 'required',
            'link' => 'required'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "Invalid_Parameter";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        $user = Auth::user();
        $id = $request->id;
        $microlink = Microlink::where('id', $id)->where('user_id', '=', $user->id)->first();
        if($microlink == null){
            $result['code'] = "microlink_not_exist";
            $result['message'] = '微链接不存在!';
            return response()->json($result);
        }
        $microlink->delete();
        $result['code'] = "Success";
        $result['message'] = '删除成功';
        return response()->json($result);
    }


    /**
     * 删除
     * @param  Request $request 
     * @return string           
     */
    public function icons(Request $request){
        $icons = $request->icons;
        foreach ($icons as $key => $icon) {
            $Icon = new Icon();
            $Icon->svg = $icon['svg'];
            $Icon->save();
        }
        $result['code'] = "Success";
        $result['message'] = '删除成功';
        return response()->json($result);
    }
}
