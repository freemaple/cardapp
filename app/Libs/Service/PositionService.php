<?php
namespace App\Libs\Service;

use Validator;
use DB;
use App\Models\Position\Provice as ProviceModel;
use App\Models\Position\City as CityModel;
use App\Models\Position\County as CountyModel;
use App\Models\Position\Town as TownModel;
use App\Models\Position\Village as VillageModel;


class PositionService
{
 
    public static function provices(){
        $provices = ProviceModel::get();
        return $provices;
    }  

    public static function getAllCity(){
        $city = CityModel::get()->toArray();
        return $city;
    }  

    public static function getCity($province_id){
        $city = CityModel::where('province_id', $province_id)->get();
        return $city;
    }

    public static function getAllCounty(){
        $county = CountyModel::get()->toArray();
        return $county;
    }

    public static function getCounty($city_id){
        $county = CountyModel::where('city_id', $city_id)->get();
        return $county;
    }

    public static function getTown($county_id){
        $town = TownModel::where('county_id', $county_id)->get();
        return $town;
    }

    public static function getVillage($town_id){
        $village = VillageModel::where('town_id', $town_id)->get();
        return $village;
    }
}