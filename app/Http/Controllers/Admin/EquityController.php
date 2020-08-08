<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Equity\Equity as EquityModel;
use App\Models\Equity\EquityRecord as EquityRecordModel;
use App\Models\Equity\EquityConfig as EquityConfig;

class EquityController extends BaseController
{

     /**
     * 后台系统首页
     *
     * @return void
     */
    public function index(Request $request)
    {
        $EquityModel = EquityModel::select('equity.*', 'user.fullname', 'user.avatar', 'user.phone')->join('user', 'user.id', '=', 'equity.user_id');


        $phone = trim($request->phone);

        if($phone != null){
            $EquityModel = $EquityModel->where('phone', '=', $phone);
        }

        $fullname = trim($request->fullname);

        if($fullname != null){
            $EquityModel = $EquityModel->where('fullname', '=', $fullname);
        }

        $pageSize = 20;

        $form = $request->all();

        $equitys = $EquityModel->orderBy('created_at', 'desc')->paginate($pageSize);

        $equitys->appends($request->all());

        $pager = $equitys->links();

        $equity_count = EquityModel::sum('equity_value');

        $equity_number = EquityModel::count();

        $view = View('admin.equity.index');

        $view->with("equitys", $equitys);

        $view->with("equity_count", $equity_count);

        $view->with("equity_number", $equity_number);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "股权");

        return $view;
    }

    /**
     * 后台系统首页
     *
     * @return void
     */
    public function record(Request $request)
    {
        $EquityRecordModel = EquityRecordModel::select('equity_record.*', 'user.fullname', 'user.avatar', 'user.phone')->join('user', 'user.id', '=', 'equity_record.user_id');

        $phone = trim($request->phone);

        if($phone != null){
            $EquityRecordModel = $EquityRecordModel->where('phone', '=', $phone);
        }

        $fullname = trim($request->fullname);

        if($fullname != null){
            $EquityRecordModel = $EquityRecordModel->where('fullname', '=', $fullname);
        }

        $pageSize = 20;

        $form = $request->all();

        $equity_record = $EquityRecordModel->orderBy('created_at', 'desc')->paginate($pageSize);

        $equity_record->appends($request->all());

        $pager = $equity_record->links();

        $view = View('admin.equity.record');

        $view->with("equity_record", $equity_record);

        $view->with("form", $form);

        $view->with("pager", $pager);

        $view->with("title", "股权赠送记录");

        return $view;
    }



     /**
     * 股权配置
     *
     * @return void
     */
    public function config(Request $request)
    {
        $config = EquityConfig::first();

        $view = View('admin.equity.config');

        $view->with("config", $config);

        $view->with("title", "股权配置");

        return $view;
    }

     /**
     * 后台系统首页
     *
     * @return void
     */
    public function saveConfig(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'vip_equity_value' => 'Numeric|min:0',
            'vip_equity_number' => 'Numeric|min:0',
            'vip_comm_equity_value1' => 'Numeric|min:0',
            'store_equity_number' => 'Numeric|min:0',
            'store_equity_value' => 'Numeric|min:0',
            'store_comm_equity_value1' => 'Numeric|min:0'
        ]);

        if($validator->fails()){

            return redirect(route("admin_equityconfig"));
        }

        $config = EquityConfig::first();

        if($config == null){
            $config = new EquityConfig();
        }

        $config->vip_equity_value = $request->vip_equity_value;

        $config->vip_equity_number = $request->vip_equity_number;

        $config->vip_comm_equity_value1 = $request->vip_comm_equity_value1;

        $config->store_equity_number = $request->store_equity_number;

        $config->store_equity_value = $request->store_equity_value;

        $config->store_comm_equity_value1 = $request->store_comm_equity_value1;

        $config->save();

        return redirect(route("admin_equityconfig"));
    }

}