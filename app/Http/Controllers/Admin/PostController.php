<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;

use App\Models\Post\Post as PostModel;
use App\Models\User\User as UserModel;
use App\Cache\Post as PostCache;

class PostController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $PostModel = PostModel::select('post.*', 'category.name as category_name')->join('category', 'post.category_id', '=', 'category.id');

        $pageSize = 20;

        $form = $request->all();

        $name = trim($request->name);

        if($name != null){
            $PostModel = $PostModel->where('post.name', '=', $name);
        }

        $fullname = trim($request->fullname);

        if($fullname != null){
            $PostModel = $PostModel->join('user', 'user.id', '=', 'post.user_id');
            $PostModel = $PostModel->where('user.fullname', 'like', '%'. sprintf("%s", $fullname). '%');
        }

        $user_id = trim($request->user_id);

        if($user_id != null){
            $PostModel = $PostModel->where('post.user_id', '=', $user_id);
        }

        $status = trim($request->status);

        if($status != null){
            $PostModel = $PostModel->where('post.status', '=', $status);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $PostModel = $PostModel->where('post.created_at', '>=', $start_date);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $PostModel = $PostModel->where('post.created_at', '<=', $end_date);
        }

        $PostModel = $PostModel->where('post.delete', '!=', '1');

        $posts = $PostModel->orderBy('post.id', 'desc')
        ->paginate($pageSize);

        $posts->appends($request->all());

        $pager = $posts->links();

        foreach ($posts as $key => $post) {
            $post->userinfo = UserModel::where('id', '=', $post->user_id)->first();
            $posts_image = $post->images()->where('type', '=', '1')->first();
            if($posts_image != null){
                $post->image = \HelperImage::storagePath($posts_image['image']);
            }
        }

        $level_status = config('user.level_status');

        $view = View('admin.post.index');

        $view->with("posts", $posts);

        $view->with("level_status", $level_status);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "文章");

        return $view;
    }

    /**
     * 加入文库
     *
     * @return void
    */
    public function in_article(Request $request)
    {
        $result = [];

        $post_id = $request->post_id;

        $post = PostModel::where('id', '=', $post_id)->first();

        if(empty($post)){
            $result['code'] = '2x1';
            $result['message'] = '文章不存在';
            return response()->json($result);
        }
        if($post['in_article'] == '1'){
            $post['in_article'] = '0';
        } else {
            $post['in_article'] = '1';
        }
        $post->save();
        $result['code'] = '200';
        $result['message'] = '操作成功！';
        return response()->json($result);
    }

      /* 删除文章
     *
     * @return void
    */
    public function remove(Request $request)
    {
        $result = [];

        $post_id = $request->post_id;

        $post = PostModel::where('id', '=', $post_id)->first();

        if(empty($post)){
            $result['code'] = '2x1';
            $result['message'] = '文章不存在';
            return response()->json($result);
        }
        $post->delete = '1';
        $post->save();
        $result['code'] = '200';
        $result['message'] = '删除成功！';
        PostCache::clearPostCache($post['post_number']);
        return response()->json($result);
    }
}