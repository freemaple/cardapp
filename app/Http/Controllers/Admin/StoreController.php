<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Helper;
use Hash;
use Validator;
use Auth;

use App\Models\User\User as UserModel;

use App\Models\Store\Store as StoreModel;

use App\Models\Store\StoreApprovalRecord;

class StoreController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function index(Request $request)
    {
        $StoreModel = new StoreModel();

        $pageSize = 20;

        $form = $request->all();

        $name = trim($request->name);

        if($name != null){
            $StoreModel = $StoreModel->where('name', '=', $name);
        }

        $status = trim($request->status);

        if($status != null){
            $StoreModel = $StoreModel->where('status', '=', $status);
        }

        $start_date = trim($request->start_date);

        if($start_date != null){
            $StoreModel = $StoreModel->where('created_at', '>=', $start_date);
        }

        $end_date = trim($request->end_date);

        if($end_date != null){
            $StoreModel = $StoreModel->where('created_at', '<=', $end_date);
        }

        $store_expires_status = $request->store_expires_status;

        if($store_expires_status != null){
            if($store_expires_status == '0'){
                $date = date('Y-m-d H:i:s');
                $StoreModel = $StoreModel->where('is_pay', '=', '0')
                ->where('expire_date', '<', $date);
            }
            if($store_expires_status == '1'){
                $date = date('Y-m-d H:i:s');
                $StoreModel = $StoreModel->where('is_pay', '=', '1')
                ->where('expire_date', '<', $date);
            }
        }

        $stores = $StoreModel->orderBy('id', 'desc')
        ->paginate($pageSize);

        $stores->appends($request->all());

        $pager = $stores->links();

        foreach ($stores as $key => $store) {
            $user_id = $store->user_id;
            $store->userinfo = UserModel::where('id', '=', $user_id)->first();
        }

        $level_status = config('user.level_status');

        $store_status = config('store.status');

        $store_level_text = config('store.level_text');

        $view = View('admin.store.index');

        $view->with("stores", $stores);

        $view->with("level_status", $level_status);

        $view->with("store_status", $store_status);

        $view->with("store_level_text", $store_level_text);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "店铺申请");

        return $view;

    }

    /**
     * 用户列表
     *
     * @return void
    */
    public function handerApply(Request $request)
    {
        $store_id = $request->id;
        $store = StoreModel::where('id', $store_id)->first();
        if($store == null){
            $result['code'] = '2x1';
            $result['message'] = '店铺不存在！';
            return response()->json($result);
        }
        $approval = $request->approval;
        $remarks = trim($request->remarks);
        if($approval == 0){
            if($remarks == ''){
                $result['code'] = '2x1';
                $result['message'] = '请填写原因！';
                return response()->json($result);
            }
            $store->status = '-1';
            $store->denial_reason = $remarks;
            $store->denial_time = date('Y-m-d H:m:s');
            $store->save();
        } else if($approval == 1){
            $store->status = '2';
            if($store['is_history_approval'] != '1'){
                $store->is_history_approval = '1';
            }
            $store->approval_time = date('Y-m-d H:m:s');
            $store->save();
        }

        $StoreApprovalRecord = new StoreApprovalRecord();

        $StoreApprovalRecord->admin_id = $this->admin_user->id;

        $StoreApprovalRecord->store_id = $store->id;

        $StoreApprovalRecord->approval_status = $approval == 1 ? '1' : '0';

        $StoreApprovalRecord->denial_reason = $approval == 0 ? $remarks : '';

        $StoreApprovalRecord->save();

        if(empty($store->expire_date) && $store->is_pay != '1'){
            $store->open_time = date("Y-m-d H:i:s");
            $next_time = strtotime(date("Y-m-d", strtotime("+1 day")));
            $new_expire_time = strtotime('+31 day', $next_time);
            $store->expire_date = date('Y-m-d H:i:s', $new_expire_time);
            $store->store_status = '1';
            $store->save();
        } 
       

        $result['code'] = '200';
        $result['message'] = '已审批！';
        return response()->json($result);
    }
}