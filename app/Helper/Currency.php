<?php

namespace App\Helper;

class Currency
{   
    /**
     *货币
     * @param string 
     * @return string
     */
	public static function fixed($price)
    {
    	return '￥' . $price;
    }
}