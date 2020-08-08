<?php

namespace App\Http\Controllers\Api;

use App\Libs\Service\PostService;
use Illuminate\Http\Request;
use Helper;
use App\Models\User\User as UserModel;

use App\Models\Store\Store as StoreModel;

class MerchantController extends BaseController
{

    /**
     * 用户列表
     *
     * @return void
    */
    public function getList(Request $request)
    {

        $province = $request->province;

        $city = $request->city;

        $district = $request->district;

        $keyword = $request->keyword;

        $pageSize = config('paginate.merchant', 100);

        $merchants = new StoreModel();
        if($province){
            $merchants = $merchants->where('provice', '=', $province);  
        }
        if(!empty($request->provice_id)){
            $merchants = $merchants->where('provice_id', '=', $request->provice_id);  
        }
        if($city){
            $merchants = $merchants->where('city', '=', $city);  
        }
        if(!empty($request->city_id)){
            $merchants = $merchants->where('city_id', '=', $request->city_id);  
        }
        if($district){
            $merchants = $merchants->where('district', '=', $district);  
        }
        if(!empty($request->district_id)){
            $merchants = $merchants->where('district_id', '=', $request->district_id);  
        }

        if(!empty($request->town_id)){
            $merchants = $merchants->where('town_id', '=', $request->town_id);  
        }

        if(!empty($request->village_id)){
            $merchants = $merchants->where('village_id', '=', $request->village_id);  
        }

        if($keyword){
            $merchants = $merchants->where('name', 'like', '%'. sprintf("%s", $keyword). '%');  
        }

        $date = date('Y-m-d H:i:s');

        $merchants = $merchants->where('status', '=', '2')
        ->where('expire_date', '!=', '')
        ->where('expire_date', '>=', $date)
        ->orderBy('id','desc')->paginate($pageSize);

        $merchants = $merchants->toArray();

        $view = view('merchant.block.list')->with('merchants', $merchants['data'])->render();

        unset($merchants['data']);

        $result = ['code' => 'Success', 'view' => $view, 'data' => $merchants];

        return response()->json($result);
    }
}