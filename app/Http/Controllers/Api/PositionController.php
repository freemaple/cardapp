<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use Validator;
use App\Models\Position\Provice as ProviceModel;
use App\Models\Position\City as CityModel;
use App\Models\Position\Town as TownModel;
use App\Models\Position\Village as VillageModel;
use App\Libs\Service\PositionService;

class PositionController extends BaseController
{
	 /**
     * 城市
     *
     * @return \Illuminate\Http\Response
     */
    public function getCity(Request $request)
    {
        $result = [];

        $province_id = trim($request->province_id);

        $city = PositionService::getCity($province_id);

        $result['data'] = $city;

        $result['code'] = 'Success';

        return response()->json($result);
    }

     /**
     * 县
     *
     * @return \Illuminate\Http\Response
     */
    public function getCounty(Request $request)
    {
        $result = [];

        $city_id = trim($request->city_id);

        $county = PositionService::getCounty($city_id);

        $result['data'] = $county;

        $result['code'] = 'Success';

        return response()->json($result);
    }

     /**
     * 镇
     *
     * @return \Illuminate\Http\Response
     */
    public function getTown(Request $request)
    {
        $result = [];

        $county_id = trim($request->county_id);

        $town = PositionService::getTown($county_id);

        $result['data'] = $town;

        $result['code'] = 'Success';

        return response()->json($result);
    }

    /**
     * 村
     *
     * @return \Illuminate\Http\Response
     */
    public function getVillage(Request $request)
    {
        $result = [];

        $town_id = trim($request->town_id);

        $village = PositionService::getVillage($town_id);

        $result['data'] = $village;

        $result['code'] = 'Success';

        return response()->json($result);
    }

    public function getAddress(Request $request){

        $province_id = trim($request->province_id);

        $citys = PositionService::getCity($province_id);

        $city_id = trim($request->city_id);

        $countys = PositionService::getCounty($city_id);

        $district_id = trim($request->district_id);

        $towns = PositionService::getTown($district_id);

        $town_id = trim($request->town_id);

        $villages = PositionService::getVillage($town_id);

        $result = [];
        
        $result['data'] = [
            'citys' => $citys,
            'countys' => $countys,
            'towns' => $towns,
            'villages' => $villages
        ];
        $result['code'] = 'Success';

        return response()->json($result);
      
    }
}
