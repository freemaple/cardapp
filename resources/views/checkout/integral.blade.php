@extends('layouts.app')

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">{{ $title }}</div>
    </div>
</div>
@endsection

@section('content')
<div class="checkout-box">
    <div class="checkout-box">
        <div class="checkout-panel">
            <div class="checkout-panel-content clearfix">
                <div class="form-group">
                    <div class="form-group-label">金额</div>
                    <input type="number" maxlength="50" class="form-control amount_value" name="amount" value="" min="0" step="0.01" />
                </div>
                <div class="text-red">
                    注：有赏积分只能用于购物
                </div>
            </div>
        </div>
        <div class="checkout-panel">
            <div class="checkout-panel-header">支付方式</div>
            <div class="checkout-panel-content">
                <div class="payment-list">
                    <div class="payment-item selected" data-code="weixin">
                        <span class="weixin-logo">
                            <span class="iconfont icon-weixin-zf"></span>
                            <span>微信支付</span>
                        </span>
                        <span class="checkbox">✓</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('copyright', view('template.copyright'))

@section('footer')
    <div class="checkot-footer">
        <ul class="clearfix">
            <li class="pay-btn-block checkout-pay-submit" style="width: 100%">
                <div class="box">
                    <a href="javascript:void(0)" class="js-integral-pay">
                       支付
                    </a>
                </div>
            </li>
        </ul>
    </div>
@endsection

@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/checkout/integral.js') }}"></script>
@endsection

