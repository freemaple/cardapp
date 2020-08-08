<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use Auth;
use App\Libs\Service\PositionService;
use App\Models\User\User as UserModel;
use App\Models\User\Address as AddressModel;
use App\Models\Position\Provice as ProviceModel;
use App\Models\Position\City as CityModel;
use App\Models\Position\County as CountyModel;
use App\Models\Position\Town as TownModel;
use App\Models\Position\Village as VillageModel;

class AddressController extends BaseController
{

     public function getAddressList(){
        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }
        $address_list = AddressModel::where('user_id', '=', $session_user->id)->get();
        $result = ['code' => 'Success'];
        $result['data']['address_list'] = $address_list;
        return response()->json($result);
    }

    public function loadAddress(Request $request){
        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }
        $address_id = $request->address_id;
        $address = AddressModel::select('id', 'fullname', 'phone', 'province_id', 'city_id', 'district_id', 'address', 'is_default')->where('user_id', '=', $session_user->id)
        ->where('id', '=', $address_id)
        ->first();
        if($address != null){
            $address = $address->toArray();
        }
        $result = ['code' => 'Success'];
        $result['data']['address'] = $address;
        return response()->json($result);
    }

	 /**
     * 添加地址
     *
     * @return \Illuminate\Http\Response
     */
    public function addAddress(Request $request)
    {
        $result = [];
        
        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }

        $is_default = trim($request->is_default);
        $fullname = trim($request->fullname);
        $phone = trim($request->phone);
        $province_id = trim($request->province_id);
        $city_id = trim($request->city_id);
        $district_id = trim($request->district_id);
        $town_id = trim($request->town_id);
        $village_id = trim($request->village_id);
        $address = trim($request->address);
        $zip = trim($request->zip);

        //验证数据
        $validator = Validator::make($request->all(), [
            'is_default' => 'required|int|min:0|max:1',
            'fullname' => 'required|string',
            'phone' => 'required|string',
            'province_id' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'town_id' => 'required',
            'village_id' => 'required',
            'address' => 'required|string',
            'zip' => 'required|string'
        ]);
        
        if($validator->fails()){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = trans('api.message.invalid_data');
            return response()->json($result);
        }

        //获取用户ID
        $user_id = $session_user->id;

        //添加地址
        $AddressModel = new AddressModel();
        $AddressModel->user_id = $user_id;
        $AddressModel->is_default = $is_default;
        $AddressModel->fullname = $fullname;
        $AddressModel->phone = $phone;

        $AddressModel->province_id = $province_id;
        $province = ProviceModel::where('provice_id', $province_id)->first();
        $AddressModel->province = $province['provice_name'];

        $AddressModel->city_id = $city_id;
        $city = CityModel::where('city_id', $city_id)->first();
        $AddressModel->city = $city['city_name'];

        $AddressModel->district_id = $district_id;
        $city = CountyModel::where('county_id', $district_id)->first();
        $AddressModel->district = $city['county_name'];

        $AddressModel->town_id = $town_id;
        $town = TownModel::where('town_id', $town_id)->first();
        $AddressModel->town = $town['town_name'];

        $AddressModel->village_id = $village_id;
        $town = VillageModel::where('village_id', $village_id)->first();
        $AddressModel->village = $town['village_name'];

        $AddressModel->address = $address;
        $AddressModel->zip = $zip;
        $AddressModel->save();

        if($is_default == '1'){
            $this->setDefault($user_id, $AddressModel->id);
        }

        $result['code'] = 'Success';

        return response()->json($result);
    }

    /**
     * 更新地址
     *
     * @return \Illuminate\Http\Response
     */
    public function editAddress(Request $request)
    {
        $result = [];
        
        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }

        $id = trim($request->id);
        $is_default = trim($request->is_default);
        $fullname = trim($request->fullname);
        $phone = trim($request->phone);
        $province_id = trim($request->province_id);
        $city_id = trim($request->city_id);
        $district_id = trim($request->district_id);
        $town_id = trim($request->town_id);
        $village_id = trim($request->village_id);
        $address = trim($request->address);
        $zip = trim($request->zip);

        //验证数据
        $validator = Validator::make($request->all(), [
            'address' => 'required|int|min:0|max:1',
            'fullname' => 'required|string',
            'phone' => 'required|string',
            'province_id' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'town_id' => 'required',
            'village_id' => 'required',
            'address' => 'required|string',
            'zip' => 'required|string'
        ]);
        
        if($validator->fails()){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = trans('api.message.invalid_data');
            return response()->json($result);
        }

        //获取用户ID
        $user_id = $session_user->id;

       //添加地址
        $AddressModel = AddressModel::where('id', $id)->where('user_id', '=', $user_id)->first();

        if($AddressModel == null){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = trans('api.message.invalid_data');
            return response()->json($result);
        }

        $AddressModel->user_id = $user_id;
        $AddressModel->is_default = $is_default;
        $AddressModel->fullname = $fullname;
        $AddressModel->phone = $phone;

        $AddressModel->province_id = $province_id;
        $province = ProviceModel::where('provice_id', $province_id)->first();
        $AddressModel->province = $province['provice_name'];

        $AddressModel->city_id = $city_id;
        $city = CityModel::where('city_id', $city_id)->first();
        $AddressModel->city = $city['city_name'];

        $AddressModel->district_id = $district_id;
        $city = CountyModel::where('county_id', $district_id)->first();
        $AddressModel->district = $city['county_name'];

        $AddressModel->town_id = $town_id;
        $town = TownModel::where('town_id', $town_id)->first();
        $AddressModel->town = $town['town_name'];

        $AddressModel->village_id = $village_id;
        $town = VillageModel::where('village_id', $village_id)->first();
        $AddressModel->village = $town['village_name'];
        $AddressModel->address = $address;
        $AddressModel->zip = $zip;
        $AddressModel->save();

        if($is_default == '1'){
            $this->setDefault($user_id, $id);
        }

        $result['code'] = 'Success';

        return response()->json($result);
    }

    /**
     * 删除地址
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAddress(Request $request)
    {
        $result = [];
        
        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }

        //获取地址ID
        $id = $request->id;

        //验证数据
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);
        
        if($validator->fails()){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = trans('api.message.invalid_data');
            return response()->json($result);
        }

        //获取用户ID
        $user_id = $session_user->id;

        
        //获取用户ID
        $user_id = $session_user->id;

        //删除地址
        $AddressModel = AddressModel::where('id', $id)->where('user_id', '=', $user_id)->first();

        if($AddressModel == null){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = trans('api.message.invalid_data');
            return response()->json($result);
        }

        $result_delete = $AddressModel->delete();

        if($result_delete){
            $result['code'] = 'Success';
            $result['message'] = '删除成功！';
        }else{
            $result['code'] = 'RECORD_NOT_EXIST';
            $result['message'] = trans('api.message.record_not_exist');
        }
        
        return response()->json($result);
    }

    /**
     * 设置默认选择地址
     *
     * @return \Illuminate\Http\Response
     */
    public function setDefaultAddress(Request $request)
    {
        $result = [];
        
        //登录验证
        $session_user = \Auth::user();
        if($session_user == null){
            return response()->json($result);
        }

        //获取地址ID
        $id = $request->id;

        //验证数据
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|int|min:1'
        ]);
        
        if($validator->fails()){
            $result['code'] = 'INVALID_DATA';
            $result['message'] = trans('api.message.invalid_data');
            return response()->json($result);
        }

        //获取用户ID
        $user_id = $session_user->id;

        //设置默认选择地址
        
        $AddressModel = AddressModel::where('id', $id)->where('user_id', '=', $user_id)->first();

        if($AddressModel == null){
            $result['code'] = 'RECORD_NOT_EXIST';
            $result['message'] = trans('api.message.invalid_data');
            return response()->json($result);
        }

        $AddressModel->is_default = '1';

        $AddressModel->save();

        $this->setDefault($user_id, $id);

        $result['code'] = 'Success';
        $result['message'] = '设置默认地址成功!';
        
        return response()->json($result);
    }

    public function setDefault($user_id, $id){
        AddressModel::where('user_id', '=', $user_id)
        ->where('is_default', '=', '1')
        ->where('id', '!=', $id)
        ->update([
            'is_default' => '0'
        ]);
    }

     public function region(){
        $result = ['code' => 'Success'];
        $provices = PositionService::provices()->toArray();
        $citys =  PositionService::getAllCity();
        $countys =  PositionService::getAllCounty();
        $provice_list = [];
        foreach ($provices as $key => $provice) {
            $provice['child'] = [];
            foreach($citys as $ckey => $city){
                $city['child'] = [];
                if($city['province_id'] == $provice['provice_id']){
                    foreach ($countys as $key => $county) {
                        if($county['city_id'] == $city['city_id']){
                            $city['child'][] = [
                                "parent_id" =>  $city['city_id'],
                                "region_id" => $county['county_id'],
                                "region_name" => $county['county_name'],
                                "region_type" => "3"
                            ];
                        }
                    }
                    $provice['child'][] = [
                        "parent_id" =>  $provice['provice_id'],
                        "region_id" => $city['city_id'],
                        "region_name" => $city['city_name'],
                        "region_type" => "2",
                        "child" => $city['child']
                    ];
                }
            }
            $provice_list[] = [
                "parent_id" =>  0,
                "region_id" => $provice['provice_id'],
                "region_name" => $provice['provice_name'],
                "region_type" => "1",
                "child" => $provice['child']
            ];
        }
        $result['data'][] = [
            "parent_id" =>  "0",
            "region_id" => "1",
            "region_name" => "中国",
            "region_type" => "0",
            "child" => $provice_list
        ];
        return response()->json($result);
    }
}
