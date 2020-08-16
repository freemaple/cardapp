<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Libs\Service\PostService;
use Auth;
use App\Models\Post\Post as PostModel;
use App\Models\Post\Reprint as PostReprintModel;

class PostController extends BaseController
{
    public function getList(Request $request){

        $user = Auth::user();

        $pageSize = 100;
        
        $posts = PostService::getUserPostList($request, $pageSize);


        $result = ['code' => 'Success',  'data' => []];

        if($request->request_type == 'app'){
            $result['data']['posts'] = $posts;
            $result['data']['user'] = [
                'id' =>  $user['id'],
                'is_vip' =>  $user['is_vip'],
                'level_status_value' =>  $user['level_status'],
            ];
        } else {
            $view = view('account.post.block.list')->with('posts', $posts)->with('session_user', $user)->render();
            $result['view'] = $view;
        }

        return response()->json($result);
    }
	
    /**
     * 保存文章
     * @param  Request $request 
     * @return string           
     */
    public function saveInfo(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'name' => 'required|max:64',
            'category_id' => 'int|min:1'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
    	$user = \Auth::user();
        //保存基本信息
    	$result = PostService::saveInfo($user, $request);
    	return response()->json($result);
    }

    /**
     * 删除文章
     * @param  Request $request 
     * @return string           
     */
    public function deletePost(Request $request){
        $post_id = $request->post_id;
        $user = Auth::user();
        $user_id = $user->id;
        $post = PostModel::where('id', $post_id)->where('user_id', '=', $user_id)
        ->where('delete', '!=', '1')->first();
        if($post == null){
            $result['code'] = "2x1";
            $result['message'] = '文章不存在！';
            return response()->json($result);
        }
        $post->delete = '1';
        $post->save();
        $result['code'] = "Success";
        $result['message'] = '文章删除成功！';
        return response()->json($result);
    }

    /**
     * 删除文章转载
     * @param  Request $request 
     * @return string           
     */
    public function deletePostReprint(Request $request){
        $user = Auth::user();
        $user_id = $user->id;
        $post_id = $request->post_id;
        $post_reprint = PostReprintModel::where('user_id', $user_id)->where('post_id', '=', $post_id)->first();
        if($post_reprint == null){
            $result['code'] = "0x00x1";
            $result['message'] = '文章转载不存在！无需删除';
            return response()->json($result);
        }
        $post_reprint->delete();
        $result['code'] = "Success";
        $result['message'] = '文章转载删除成功！';
        return response()->json($result);
    }

    /**
     * 转载
     * @param  Request $request 
     * @return string           
     */
    public function reprint(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'post_id' => 'required|max:64'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return response()->json($result);
        }
        $post_id = $request->post_id;
        $user = Auth::user();
        if(empty($user)){
            $result['code'] = "UNAUTH";
            $result['message'] = '未登录！';
            return response()->json($result);
        }
        if($user['is_vip'] != '1' || $user['level_status'] <= 0){
            $result['code'] = "un_vip";
            $result['message'] = '';
            return response()->json($result);
        }
        $post = PostModel::where('id', $post_id)->where('delete', '!=', '1')->first();
        if($post == null){
            $result['code'] = "0x00x1";
            $result['message'] = '文章不存在！';
            return response()->json($result);
        }

        if($post['user_id'] == $user->id){
            $result['code'] = "0x00x1";
            $result['message'] = '您的文章，无需创建成您的！';
            return response()->json($result);
        }

        $user_id = $user->id;

        $post_reprint = PostReprintModel::where('user_id', $user_id)->where('post_id', '=', $post_id)->first();

        if($post_reprint == null){
            $post_reprint = new PostReprintModel();
            $post_reprint->user_id = $user_id;
            $post_reprint->post_id = $post_id;
            $post_reprint->save();
        }

        $data = [];

        $data['id'] = $post_reprint['id'];

        $result['data'] = $data;

        $result['code'] = "Success";
        $result['message'] = '';

        return response()->json($result);
    }

    /**
     * 保存名片
     * @param  Request $request 
     * @return string           
     */
    public function getBeautyPost(Request $request){
        $data = $request->all();
        //数据校验
        $validator = Validator::make($data, [
            'category_id' => 'required|int|min:1'
        ]);
        //数据校验失败
        if($validator->fails()){
            $result['code'] = "0x00x1";
            $result['message'] = implode("<br />", $validator->errors()->all());
            return $result;
        }
        //保存基本信息
        $posts = PostService::getBeautyPost();
        $pagesize = config('paginate.beauty_post', 50);
        $view = view('article.block.post', ['posts' => $posts])->render();
        $result = ['code' => 'Success'];
        $result['view'] = $view;
        return response()->json($result);
    }
}
