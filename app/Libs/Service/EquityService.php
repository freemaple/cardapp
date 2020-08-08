<?php
namespace App\Libs\Service;

use Hash;
use Validator;
use Helper;
use App\Models\User\User as UserModel;
use App\Models\Equity\Equity as EquityModel;
use App\Models\Equity\EquityRecord as EquityRecordModel;
use App\Models\Equity\EquityConfig;

class EquityService
{
    /**
     * 添加股权
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public static function addEquity($user = null, $data = []){
        $user_id = $user->id;
        $equity = EquityModel::where('user_id', '=', $user_id)->first();
        if($equity == null){
            $equity = new EquityModel();
            $equity->user_id = $user_id;
            $equity->equity_value = 0;
            $c_equity_value = 0;
        } else {
            $c_equity_value = $equity->equity_value;
        }
        $equity_value = $data['equity_value'];
        $equity->equity_value = $c_equity_value + $equity_value;
        $r = $equity->save();
        if($r){
            $EquityRecordModel = new EquityRecordModel();
            $EquityRecordModel->user_id = $user_id;
            $EquityRecordModel->equity_value = $equity_value;
            $EquityRecordModel->content = $data['content'];
            $EquityRecordModel->remark = $data['remark'];
            $EquityRecordModel->type = $data['type'];
            $EquityRecordModel->order_recharge_id = $data['order_recharge_id'];
            $EquityRecordModel->save();
        }
    }

     /**
     * 添加股权
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public static function addFanEquity($user = null, $data = []){
        $user_id = $user->id;
        $equity = EquityModel::where('user_id', '=', $user_id)->first();
        if($equity == null){
            $equity = new EquityModel();
            $equity->user_id = $user_id;
            $equity->equity_value = 0;
            $equity->fan_equity_value = 0;
            $c_fan_equity_value = 0;
        } else {
            $c_fan_equity_value = $equity->fan_equity_value;
        }
        $fan_equity_value = $data['fan_equity_value'];
        $equity->fan_equity_value = $c_fan_equity_value + $fan_equity_value;
        $r = $equity->save();
        if($r){
            $EquityRecordModel = new EquityRecordModel();
            $EquityRecordModel->user_id = $user_id;
            $EquityRecordModel->equity_value = $fan_equity_value;
            $EquityRecordModel->content = $data['content'];
            $EquityRecordModel->remark = $data['remark'];
            $EquityRecordModel->type = $data['type'];
            $EquityRecordModel->order_recharge_id = $data['order_recharge_id'];
            $EquityRecordModel->save();
        }
    }


    /**
     * 添加股权
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public static function vipEquity($order_recharge, $user){
        if($order_recharge['equity_account'] == '1'){
            return false;
        }
        $is_equity = false;
        $config = EquityConfig::first();
        if($config == null){
            $order_recharge->equity_account = '1';
            $order_recharge->save();
            return false;
        }
        if($config['vip_equity_number'] > 0){
            $user_id = $user->id;
            $EquityRecordModel = EquityRecordModel::where('user_id', $user_id)
            ->where('type', 'open_vip')->first();
            if($EquityRecordModel == null && $config['vip_equity_value'] > 0){
                $data = [
                    "order_recharge_id" => $order_recharge->id,
                    "equity_value" => $config['vip_equity_value'],
                    "content" => '开通ip赠送',
                    "remark" => '开通ip赠送',
                    "type"   => 'open_vip'
                ];
                static::addEquity($user, $data);
                $is_equity = true;
            }
            if($config['vip_comm_equity_value1'] > 0){
                $referrer_user_id = $user->referrer_user_id;
                if($referrer_user_id > 0){
                    $referrer_user = UserModel::where('id', $referrer_user_id)->first();
                    if($referrer_user != null){
                        $rdata = [
                            "order_recharge_id" => $order_recharge->id,
                            "equity_value" => $config['vip_comm_equity_value1'],
                            "content" => '好友开通vip赠送',
                            "remark" => '好友开通vip赠送',
                            "type"   => 'first_commission_open_vip'
                        ];
                        static::addEquity($referrer_user, $rdata);
                    }
                }
            }
            if($is_equity){
                $config->vip_equity_number = $config->vip_equity_number - 1;
                if($config->vip_equity_number <0){
                    $config->vip_equity_number = 0;
                }
                $config->save();
            }
           
        }
        $order_recharge->equity_account = '1';
        $order_recharge->save();
    }

    /**
     * 添加股权
     * @param  object $user UserModel
     * @param  array  $data 
     * @return array
     */
    public static function storeEquity($order_recharge, $user){
        if($order_recharge['equity_account'] == '1'){
            return false;
        }
        $is_equity = false;
        $config = EquityConfig::first();
        if($config == null){
            $order_recharge->equity_account = '1';
            $order_recharge->save();
            return false;
        }
        if($config['store_equity_number'] > 0){
            $user_id = $user->id;
            $EquityRecordModel = EquityRecordModel::where('user_id', $user_id)
            ->where('type', 'open_store')->first();
            if($EquityRecordModel == null && $config['store_equity_value'] > 0){
                $data = [
                    "order_recharge_id" => $order_recharge->id,
                    "equity_value" => $config['store_equity_value'],
                    "content" => '开通店铺赠送',
                    "remark" => '开通店铺赠送',
                    "type"   => 'open_store'
                ];
                static::addEquity($user, $data);
                $is_equity = true;
            }
            if($config['store_comm_equity_value1'] > 0){
                $referrer_user_id = $user->referrer_user_id;
                if($referrer_user_id > 0){
                    $referrer_user = UserModel::where('id', $referrer_user_id)->first();
                    if($referrer_user != null){
                        $rdata = [
                            "order_recharge_id" => $order_recharge->id,
                            "equity_value" => $config['store_comm_equity_value1'],
                            "content" => '好友开通店铺赠送',
                            "remark" => '好友开通店铺赠送',
                            "type"   => 'first_commission_open_store'
                        ];
                        static::addEquity($referrer_user, $rdata);
                    }
                }
            }
            if($is_equity){
                $config->store_equity_number = $config->store_equity_number - 1;
                if($config->store_equity_number <0){
                    $config->store_equity_number = 0;
                }
                $config->save();
            }
        }
        $order_recharge->equity_account = '1';
        $order_recharge->save();
    }
}