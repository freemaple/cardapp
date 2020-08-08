<?php
namespace App\Libs\Service;

use App\Models\Order\OrderCommission as OrderCommissionModel;
use Validator;

class CommissionService
{

    /**
     * 添加
     * @param  array $data 
     * @return array
     */
    public static function insert($data)
    {
        $OrderCommission = OrderCommissionModel::where('order_id', '=', $data['order_id'])->where('user_id', $data['user_id'])->first();
        if($OrderCommission == null){
            $OrderCommission = new OrderCommissionModel();
        }
        foreach ($data as $key => $value) {
            $OrderCommission->$key = $value;
        }
        $result = $OrderCommission->save();
        return $result;
    }
}