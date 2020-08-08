@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('styles')
<style type="text/css">
	.amount_value {
		font-size: 0.30rem;
		color: #f00;
		margin-left: 2px;
	}
	.text-tip {
		font-size: 0.24rem;
	}
	.a-link {
		font-size: 0.24rem;
	}
	.icon-box-huoban {
		background: #03a9f4
	}
	.icon-box-rupgrade {
		background: #00bcd4
	}
	.icon-box-wx {
		background: #33e63a
	}
	.icon-box-help {
		background: #ffc107
	}
	.icon-box-wish {
		background: #ffc107
	}
	.icon-box-xuexi {
		background: #03a9f4
	}
	.copyright-box {
		display: none;
	}
	.my-service{
		padding: 10px 0 0;
		margin-top: 10px;
		background: #fff;
	}
	.sec-header{
		display:flex;
		align-items: center;
		font-size: 0.28rem;
		padding: 5px 10px;
	}

	.service-list{
		display: flex;
		flex-wrap: wrap;
		padding: 0 0 10px;
		margin-top: 10px;
		justify-content: flex-start;
	}
	.service-list-item{
		width: 25%;
		text-align: center;
		margin-bottom: 20px;
	}
	.service-list-item .icon-box{
		display: block;
		width: 44px;
		margin: auto;
		height: 44px;
		line-height: 44px;
		border-radius: 50%;
		font-size: 32px;
		color: #ffffff;
	}
	.service-list-item .icon-box .iconfont {
		font-size: 26px;
	}
	.service-desc{
		margin-top: 10px;
		color: #414141;
	}
	.icon-gift {
		font-size: 20px;
		color: #009688;
	}
	.icon-ziyuan {
		font-size: 20px;
		color: #009688;
	}
	.icon-jifen {
		color: #009688;
		font-size: 20px;
	}
	.icon-wallet {
		color: #009688;
		font-size: 24px;
	}
	.box-list {
		display: flex;
		background-color: #ffffff;
		margin-bottom: 10px;
	}
	.box-item {
		width: 50%;
		text-align: center;
		padding: 10px 0px;
		flex: 1;
		line-height: 22px;
	}
	.box-item:first-child {
		background-color: #06e40f;
		color: #fff
	}
	.box-item .icon-share {
		color: #fff;
	}
	.box-item a {
		color: #fff;
	}
	.box-item:last-child {
		background-color: #f44336;
		color: #fff
	}
</style>
@endsection

@section('content')
<div class="account-warp">
	<div class="account-home-info clearfix">
		<div class="avatar-info">
		 	<form class="upload-form avatar-upload-form" method="post"   enctype="multipart/form-data" action="/api/user/changeavatar">
		        <a class="js-change-avatar">
		            <img src="{{ HelperImage::getavatar($user->avatar) }}" />
					<input name="image" accept="image/*" type="file" class="upload-file avatar-upload-file" />
					<input type="hidden" name="_token" class="art_upload_form_token" value="{{ csrf_token() }}" />
		        </a>
	        </form>
        </div>
        <div class="username">
            <div class="name">
            	{{ str_limit($user['fullname'], 8) }}
            	@if($user['level_status'] >= 1)
            	<span class="level-status-text" style="color: {{ $vip_color[$user['level_status']] or '#ff9800' }};border: 1px solid {{ $vip_color[$user['level_status']] or '#ff9800' }};">{{ $level_status[$user['level_status']] }}</span>
            	<span class="vip_end_date">
            		@if($vip_end_day > 0)余{{ $vip_end_day }}天到期 @else 已到期 @endif
            	</span> 
            	@endif
            </div>
            @if($vip_end_day <= 0)
            <div><span class="value">到期时间 {{ $user['vip_end_date'] }}</span></div>
            @endif
            <div><span class="value">{{ $user['created_at'] }}</span></div>
            <div>手机： <span class="">{{ $user['phone'] }}</span></div>
            <div>登录用户名： <span class="">{{ $user['user_name'] }}</span></div>
            @if(!empty($referrer_user))
            <div>
            	我的辅导专员： <span class="">{{ Helper::hideNameStar($referrer_user['fullname']) }}</span>
            </div>
            @endif
            @if($user['level_status'] > 0)
            <span>荣誉功勋(金:钻): </span><span class="honor_value">{{ $honor_value }} : {{ $honor_vip_value }}</span>
			<span style="color: #999;font-size: 0.22rem">(满5：0为钻麦)</span>
			@endif
			@if($manager_commission > 0)
            <div><span>稻田管理积分: </span><span class="honor_value text-red" style="font-size: 0.3rem">￥{{ $manager_commission }}</span></div>
			@endif
        </div>
       	<div class="vip-info">
       		@if($user['is_vip'])
	        <div>
	        	<a class="operate-btn" href="{{ Helper::route('account_vipUpgrade', ['vip_type' => 'renewal']) }}">抢礼包</a>
	        </div>
	        @else
	        	<a class="operate-btn" href="{{ Helper::route('account_vipUpgrade') }}">抢礼包</a>
	        @endif
        </div>
    </div>
    @if(!$user->is_vip)
	<div>
		<a href="{{ Helper::route('account_vipUpgrade') }}">
			<img src="/media/images/vip_gift.jpg" style="width: 100%">
		</a>
	</div>
	@endif
	@if($user->is_vip)
	<div class="rw-box clearfix" style="background: #ffffff;padding: 10px;margin-top: 10px">
		<span>今日分享任务：</span><span class="text-red">{{ $today_task_status_text }}</span>
		@if($today_task_status == '0')
		<a href="{{ $share_product_link }}" style="color: #f00;float: right;">去完成</a>
		@endif
		<div style="font-size: 0.22rem;color: #444444;margin-top: 10px">
			任务流程：（1）点击去完成（2）右上角【分享售卖】，分享到“微信朋友圈”（3）在朋友圈中，点开链接，浏览一次，即完成任务！ (4) 返回个人中心查看任务完成情况
		</div>
		<div class="text-red"  style="font-size: 0.22rem;margin-top: 5px">提示:分享保留至今晚24点生效！方可享受双重红利和每日赠送20代购积分！好友自购赚大红包，你也赚大红包！多卖多赚！加油哦！</div>
	</div>
	@endif
	<div class="account-panel">
		<div class="account-panel-header clearfix">
			<a href="{{ Helper::route('account_orders') }}">我的订单
				<span class="pull-right"><span class="iconfont icon-to-right"></span>></span>
			</a>
		</div>
		<div class="account-panel-content">
			<ul class="order-status-list clearfix">
				@foreach($order_status_list as $status_code => $status_code_text)
				<li class="order-status-item">
					<a href="{{ Helper::route('account_orders', ['status_code' => $status_code]) }}">
						<div class="order-status-item-box">
							<span class="iconfont icon-order-{{ strtolower($status_code) }}">
								<span class="number order-{{$status_code}}-number" style="display: none;"></span>
							</span>
							
						</div>
						<p>
							{{ $status_code_text }}
						</p>
					</a>
				</li>
				@endforeach
				<li class="order-status-item">
					<a href="{{ Helper::route('account_order_refund_list') }}">
						<div class="order-status-item-box">
							<span class="iconfont icon-order-refund">
								<span class="number order-refund-number" style="display: none;"></span>
							</span>
						</div>
						<div>
							退换单
						</div>
					</a>
				</li>
			</ul>
		</div>
	</div>
	@if($user['is_vip'])
	<div class="box-list">
		<div class="box-item">
			<a href="{{ Helper::route('account_share') }}">
				<span class="iconfont icon-share" style="font-size: 24px;"></span>
				<p>金麦邀请会员</p>
				<p>立赚20代购积分</p>
			</a>
		</div>
		<div class="box-item">
			<a href="javascript:void(0)" class="js-show-rupgrade">
				<span class="iconfont icon-weixingongzhonghao" style="font-size: 24px;"></span>
				<p>代购开通</p>
				<p>剩余代购积分{{ $user->sub_integral_amount }}</p>
			</a>
		</div>
	</div>
	@endif
	<div class="account-box-list clearfix">
		<div class="account-box">
			<div class="account-block">
				<a href="{{ Helper::route('account_reward') }}">
					<span class="iconfont icon-wallet"></span>
					<span>我的余额 </span><span class="amount_value reward_amount"></span>
				</a>
				<div>
					<a class="btn-operate" href="{{ Helper::route('account_reward') }}">明细</a>
					<a class="btn-operate js-show-reward-toIntegral" href="javascript:void(0)">转为有赏积分</a>
				</div>
			</div>
		</div>
		<div class="account-box">
			<div class="account-block">
				<span class="iconfont icon-jifen"></span>
				<span>有赏积分 </span><span class="amount_value integral_amount"></span>
				<div>
					<a class="btn-operate" href="{{ Helper::route('account_integral') }}">明细</a>
					<a class="btn-operate" href="{{ Helper::route('shop') }}">去购物</a>
					<a class="btn-operate" href="{{ Helper::route('checkout_integral') }}">充值</a>
				</div>
				{{--
				<div class="account-transfer-box">
					<span style="width: 80px;display: inline-block;"></span>
					<a class="btn-operate js-show-transfer-code" href="javascript:void(0)" data-type="{{ $user['is_vip'] }}">我的收款码</a>
					@if($user->is_vip != '1')
					<a class="btn-operate js-integral-transfer" href="javascript:void(0)" data-type="{{ $user['is_vip'] }}" data-href="{{ Helper::route('account_integral_transfer') }}">有赏积分转账</a>
					@else
					<a class="btn-operate" href="{{ Helper::route('account_integral_transfer') }}">有赏积分转账</a>
					@endif
				</div>
			   --}}
			</div>
		</div>
		<div class="account-box">
			<div class="account-block">
				<a href="javascript:void(0)">
					<span class="iconfont icon-gift"></span>
					<span style="margin-right: 5px">礼包奖励 </span><span class="amount_value" >{{ $gift_commission }}麦粒</span>
				</a>
				@if($gift_commission > 0)
				<div>
					<a class="btn-operate js-show-giftComtoReward" href="javascript:void(0)">转为余额</a>
					@if($user->is_vip == '1')
					<a class="btn-operate js-show-giftComtoGold" href="javascript:void(0)">置换金麦穗</a>
					@endif
				</div>
				@endif
			</div>
		</div>
		<div class="account-box">
			<div class="account-block">
				<a href="{{ Helper::route('account_gold') }}">
					<span class="iconfont icon-ziyuan"></span>
					<span>金麦穗资产 </span><span class="amount_value">￥{{ $user_gold['total_amount'] }}</span>
				</a>
				<div>
					<a class="btn-operate" href="{{ Helper::route('account_gold') }}">明细</a>
				</div>
			</div>
		</div>
	</div>
	<!-- 我的服务 -->
	<div class="my-service icon info-box">
		<div class="sec-header">
			<text>我的服务</text>
		</div>
		<div class="service-list">
			@foreach($serviceList as $key => $item)
			@if(!isset($item['is_vip']) || $user['is_vip'])
			<div class="service-list-item">
				<a href="{{ $item['url'] }}"  @if(!empty($item['data_url'])) data-href="{{ $item['data_url'] }}" @endif class="{{ $item['a_class'] or '' }}">
					<div class="service-item">
						<div>
							<span class="icon-box icon-box-{{$item['name']}}">
								<span class="iconfont {{$item['icon']}}"></span>
							</span>
						</div>
						<div class="service-desc">{{$item['desc']}}</div>
					</div>
				</a>
			</div>
			@endif
			@endforeach
		</div>
	</div>
	@if($session_user['is_vip'] == '1')
	<div style="margin: 0px 0px 20px 0px;text-align: center;background: #ffffff;padding: 20px 0px">
		<img src="{{ $link_qrcode or '' }}" width="120" id="qr_image" />
		<p>邀请二维码</p>
		<p style="margin-top: 10px;">
			<a href="{{ Helper::route('account_share') }}" class="btn btn-primary">分享海报</a>
		</p>
	</div>
	@endif
</div>
@section('copyright', view('template.copyright'))
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script type="text/template" id="toIntegral-template">
	<div class="reward-toIntegral-box" style="padding: 10px;max-width: 95%;width: 360px">
		<div class="toIntegral-header">余额转为有赏积分</div>
	    <div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>金额</div>
			<input type="number" class="form-control reward_to_amount" required="required" name="amount" min="0" step="0.01"  />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control reward_transaction_password" type="password" name="transaction_password" required="required" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div class="form-group">
			<span class="text-info text-tip">注：有赏积分{{--可自由转账和--}}购物,不能转化回余额！</span>
		</div>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block js-reward-toIntegral">确认转换</a>
		</div>
	</div>
</script>
<script type="text/template" id="transfer-code-box-template">
	<div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a class="js-close-layer" href="javascript:void(0)"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">我的有赏积分收款二维码</div>
        </div>
    </div>
    <div class="qr-box pop-bt-codebox" style="background-color: #f5f5f5">
        <div style="position: absolute;top: 50%;margin-top: -150px;text-align: center;width: 100%">
            <img src="{{ $integral_qrcode }}" />

            <div style="font-size: 20px;font-weight: bold;color: #f00;line-height: 25px;margin-top: 5px">我的收款账号代码：<span style="border-bottom: 1px solid #222;padding: 2px">{{ $user['id'] }}</span></div>

            <div style="font-size: 28px;font-weight: bold;color: #fe7589;line-height: 25px;margin-top: 15px">
            	用微信扫一扫，向我转账<br />
            </div>
        </div>
    </div>
</script>
<script type="text/template" id="torupgrade-template">
	<div class="rupgrade-box" style="padding: 10px 0px 10px 10px;max-width: 100%;width: 360px;margin: auto">
		<div class="toIntegral-header">代购开通</div>
	    <div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>手机号码或者用户名</div>
			<input type="text" class="form-control check-phone" required="required" name="phone" style="width: 75%;display: inline-block;"  />
			<input type="button" class="btn btn-primary js-check-user" style="display: inline-block;" value="搜索"  />
		</div>
		<div style="min-height: 100px">
			<div class="upgrade-info" style="display: none">
				<span class="upgrade-msg text-red" style="padding: 10px; background: #f5f5f5"></span>
				<a class="btn btn-primary upgrade-link"  style="height: 34px;line-height: 34px;">确认代购开通</a>
			</div>
		</div>
		<div>剩余代购积分：{{ $user->sub_integral_amount }}</div>
	</div>
</script>
<script type="text/template" id="giftComtoReward-template">
	<div class="reward-toIntegral-box" style="padding: 10px;max-width: 95%;width: 360px">
		<div class="toIntegral-header">麦粒转为余额</div>
		<div class="form-group">
			<div class="form-group-label">当前剩余麦粒：<span class="text-red" style="font-size: 0.32rem">{{ $gift_commission }}</span> 麦粒</div>
		</div>
	    <div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>金额</div>
			<input type="number" class="form-control giftcom_to_reward_amount" required="required" name="amount" min="0" step="0.01"  />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control giftcom_to_reward_tpwd" type="password" name="transaction_password" required="required" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block js-giftComtoReward">确认转换</a>
		</div>
	</div>
</script>
<script type="text/template" id="giftComtoGold-template">
	<div class="reward-toIntegral-box" style="padding: 10px;max-width: 95%;width: 360px">
		<div class="toIntegral-header">麦粒置换金麦穗</div>
		<div class="form-group">
			<div class="form-group-label">当前剩余麦粒：<span class="text-red" style="font-size: 0.32rem">{{ $gift_commission }}</span> 麦粒</div>
		</div>
	    <div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>麦粒</div>
			<input type="number" class="form-control giftcom_to_gold_number" required="required" name="amount" min="0" step="1"  />
		</div>
		<div class="form-group">
			<div class="form-group-label"><span class="text-red">*</span>交易密码</div>
			<input class="form-control giftcom_to_gold_tpwd" type="password" name="transaction_password" required="required" maxlength="50" value="" />
		</div>
		<a href="{{ Helper::route('account_setting', ['tab'=> 'transaction_password']) }}" class="a-link">忘记/重置交易密码？如未设置先获取交易密码</a>
		<div>
			<a href="javascript:void(0)" class="btn btn-primary btn-block js-giftComtoGold">确认转换</a>
		</div>
	</div>
</script>
<script src="{{ Helper::asset_url('/media/scripts/view/account/index.js') }}"></script>
@endsection

