@extends('layouts.app')

@section('header')@endsection

@section('content')
    <style type="text/css">
        body {
           background-image: url("{{ Helper::asset_url('/media/images/sign_bg.jpg') }}");
           background-color: #f5f5f5;
           color: #fff;
        }
    </style>
    <div class="entry-box js-entry-box clearfix">
        <div class="entry-box-item entry-login-box">
            <div class="reg-pack-panel entry-panel">
                <div class="reg-tip-box">
                    <img src="{{ asset('/media/images/logo.png') }}" width="100">
                </div>
                <div class="vip-desc-box">
                    <div class="desc-title">抢购礼包双重赚福利说明：</div>
                    <ul>
                        <li>1、得到礼包产品</li>
                        <li>2、得到网店使用权限365天</li>
                        <li>3、得到礼包代购积分</li>
                        <li>4、得到礼包配送的麦粒</li>
                        <li>5、共享自营商城</li>
                    </ul>
                    <div>
                        温馨提示：购买礼包成功后，
                        第一步，先到【我的】页面，把礼包麦粒置换成“金麦穗”
                        第二步，点击【去完成】或商城首页【马上分享】去完成每天的分享任务！
                    </div>
                </div>
                <div class="sign-panel-content" style="margin-top: 5px">
                    <div class="reg-vip-box">
                        <div class="reg-vip-item"><span class="vip-t">VIP</span><span class="vip-value">一年</span></div>
                        <div class="reg-vip-item"><span class="vip-t">原价</span><span class="vip-value vip-price-old">￥{{ $vippackage['amount_org'] }}</span></div>
                        <div class="reg-vip-item">
                            <span class="vip-t"> 活动价</span><span class="vip-value vip-price-value">￥{{ $vippackage['amount'] }}</span>
                            <a class="btn btn-primary vip-open" href="{{ Helper::route('checkout_vip') }}">开通</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="previous_link" name="previous_link" value="{{ $previous_link or '' }}">
    </div>
@endsection

