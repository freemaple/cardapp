@extends('layouts.app')

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
            <div class="mobile-header-title">购买礼包</div>
        </div>
    </div>
@endsection

@section('content')
    <style type="text/css">
        body {
           /*background-image: url("{{ Helper::asset_url('/media/images/sign_bg.jpg') }}");*/
           background-color: #ffffff;
           color: #fff;
        }
        .account-p-info  {
            color: #444444;
            line-height: 20px
        }
        .avatar-info {
            float: left;
            width: 40px;
            height: 40px;
            
        }
        .avatar-info img{
            width: 40px;
            height: 40px;
            border-radius: 50%
        }
        .entry-box {
            padding: 10px 10px 40px 10px;
        }
    </style>
    <div class="entry-box js-entry-box clearfix">
        <div class="account-p-info clearfix">
            <div class="avatar-info">
               <img src="{{ HelperImage::getavatar($session_user->avatar) }}" />
            </div>
            <div>
                <div>HI, 我是人人有赏小姐姐</div>
                <div>分享一个金麦穗平台给你</div>
            </div>
        </div>
        <div style="margin: 20px 0px 0px 0px">
            <a href="{{ Helper::route('account_vipUpgrade') }}">
                <img src="/media/images/vip_gift.jpg" style="width: 100%">
            </a>
        </div>
         <div class="site-pro-header">
            购买礼包赠送VIP会员，享受金麦穗红利
        </div>
        <div class="product-list-box js-product-scroll-container">
            <ul class="clearfix product-list js-product-list">
                @foreach($gifts as $pkey => $gift)
                <li class="product-row product-item product-item-{{ $gift['id'] }}" data-id="{{ $gift['id'] }}">
                    <div class="product-item-box">
                        <a href="{{ Helper::route('account_vipUpgradeDetail', ['gift_id' => $gift['id'], 'uid' => $uid]) }}">
                            <div class="img lazy">
                                <img class="lazyload" data-img="{{ $gift['product']['sku']['image'] or '' }}" />
                            </div>
                            <div class="info">
                                <div class="name">{{ $gift['product']['name'] or '' }}</div>
                                <div class="price-info clearfix">
                                    <span class="price">
                                        ￥{{ $gift['price'] or '' }}
                                    </span>
                                    <span class="s-price">
                                        ￥{{ $gift['market_price'] or '' }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="waiting-load-block js-load-block" style="display: none">
            <div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
        </div>
    </div>
        <input type="hidden" id="previous_link" name="previous_link" value="{{ $previous_link or '' }}">
    </div>
@endsection