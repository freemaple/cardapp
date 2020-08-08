@extends('layouts.app')

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_center') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('styles')
<style type="text/css">
	.mobile-header-box {
		border-bottom: 0px;
	}
	.store-info-box {
		background-color: #fe7589;
	    background-image: -webkit-linear-gradient(bottom, #ff587f, #fe7549);
	    background-image: linear-gradient(0deg, #ff587f 0, #fe7549);
		padding: 15px 10px;
		text-align: center;
		color: #fff;
	}
	.store-info-box a {
		font-size: 0.3rem;
		color: #fff
	}
	.store-box {
		font-size: 0.26rem;
		margin-top: 0px;
	}
	.store-banner-box {
		position: relative;
		height: 3.6rem;
		background: #ffffff;
	}
	.store-banner-box .edit-banner-box {
		position: absolute;
		bottom: 0px;
		right: 0px;
		background: #fe7589;
		padding: 10px;
		color: #ffffff;
		opacity: 0.8;
		border-radius: 5px;
		-webkit-border-radius: 5px;
	}
	.c-span {
		padding-left: 5px;
		color: #f00
	}
	.s-btn {
		color: #00c;
		font-size: 16px;
		background-color: #fe5430;
		padding: 2px 4px;
	}
	.s-btn a {
		color: #fff;
	}
	.u-time-box {
		background: #ffffff;
		padding: 10px;
		margin-top: 10px
	}
	.s-name-nox {
		position: absolute;
		top: 0%;
		margin-top: 12px;
		left: 0%;
		width: 100%;
		font-size: 0.44rem;
		color: #f4fc1a;
		text-align: center;
		z-index: 1
	}
	.store-info-box .s_r_btn {
		padding-top: 4px;
		color: #0fc;
		font-size: 0.28rem;
		display: inline-block;
	}
	.store-box .s_gift {
		font-size: 0.28rem;
		color: #03a9f4
	}
	.s-order-status-panel {
		margin-top: 10px;
	}
	.order-status-box {
		padding: 20px 10px 0px 10px;
		background-color: #ffffff;
		color: #444;
		margin-top: 0px;
		font-size: 
	}
	.s-panel-header {
		padding: 10px;
		background-color: #fe7589;
	    background-image: -webkit-linear-gradient(bottom, #ff587f, #fe7549);
	    background-image: linear-gradient(0deg, #ff587f 0, #fe7549);
		color: #fff;
		width: 100%;
		font-size: 0.26rem;
		position: relative;
	}
	.s-panel-header a {
		color: #fff;
		display: block;
	}
	.s-order-status-panel .order-status-item a {
		color: #444444;
		font-size: 0.24rem;
	}
	.s-panel-header .s-add-prodcut-btn {
		color: #00f;
		font-size: 0.28rem;
	}
	.item-bbox {
		background-color: #ffffff;
		padding: 10px 0px;
	}
	.b-item-list {
		font-size: 0px;
		padding: 10px;
	}
	.b-item {
		width: 25%;
		vertical-align: top;
		display: inline-block;
		margin-bottom: 10px;
		padding-right: 10px;
	}
	@media(max-width: 360px){
		.b-item {
			width: 33.33%
		}
	}
	.b-item-box {
		padding: 10px;
		background-color: #ffffff;
		color: #444444;
		text-align: center;
		border-radius: 5px;
		-webkit-border-radius: 5px;
		border: 1px solid #eeeeee;
	}
	.b-item-box:hover {
		/*background-color: #fe7589;
	    background-image: -webkit-linear-gradient(bottom, #ff587f, #fe7549);
	    background-image: linear-gradient(0deg, #ff587f 0, #fe7549);*/
	}
	.b-item-box a {
		color: #fe7589
	}
	.b-item .text {
		font-size: 0.24rem;
		color: #444444
	}
	.b-item .value {
		font-size: 0.26rem;
		color: #fe7589
	}
	.b-item-box:hover a {
		/*color: #fff;*/
	} 
	.b-item-box:hover .text {
		/*color: #fff;*/
	} 
	.btn-item-list .b-item {
		width: 33.33%
	}
	.btn-item-list .b-item-box {
		
	}
	.btn-item-list .b-item-box .text {
		color: #444444
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
	.icon-box-shangjia {
		background: #f00
	}
	.icon-box-renew {
		background: #ffc107
	}
	.icon-box-xuexi {
		background: #03a9f4
	}
	.icon-box-store {
		background: #f00;
		color: #ffffff
	}
	.icon-box-agreement {
		background: #00f;
		color: #fff;
	}
</style>
@endsection

@section('content')
<div class="">
	<div class="store-info-box">
		@if(!empty($store))
			<a href="{{ Helper::route('account_store_info') }}" style="display: block;">
				@if($store['status'] == '0')
				进入店铺身份信息认证（<span class="text-info">待提交</span>）
				@elseif($store['status'] == '-1')
				店铺身份信息未通过 ，进入修改
				<span class="c-span">>>></span>
				@elseif($store['status'] == '1')
					店铺身份信息审核中
				@elseif($store['status'] == '2')
					店铺身份信息已审核通过
					<span style="margin-left: 5px">
						<a href="{{ Helper::route('account_store_info', ['is_recert' => '1']) }}" class="s_r_btn">重新认证</a>
					</span>
				@endif
			</a>
		@else
			<span><a href="{{ Helper::route('account_vipUpgrade') }}" style="padding: 5px 10px">立即开通店铺</a></span>
		@endif
	</div>
	<div class="store-box">
		<div class="store-item">
			<div class="img store-banner-box">
				<a href="{{ !empty($store) ? Helper::route('store_view', [$store['id']]) : Helper::route('account_vipUpgrade') }}">
					<img src="@if(empty($store['banner'])) {{ Helper::asset_url('/media/images/default_store_banner.png') }}  @else {{ HelperImage::storagePath($store['banner']) }} @endif" />
				</a>
				@if(!empty($store))
				<a class="edit-banner-box js-store-banner-upload" href="javascript:void(0)">编辑迎客封面(2:1)</a>
				@endif
			</div>
		</div>
		<div class="clearfix" style="padding: 10px;background-color: #ffffff">
			@if(!empty($store->expire_date))
			<div style="margin-top: 5px;font-size: 12px">租期到期时间：{{ $store->expire_date }}</div>
			@endif
		</div>
	</div>
</div>
@if(!empty($store))
<div class="s-order-status-panel">
	<div class="s-panel-header">
		<a href="{{ Helper::route('account_store_orders') }}">
			我的店铺订单
			<span class="to" style="position: absolute;right: 20px">></span>
		</a>
	</div>
	<div class="order-status-box">
		<ul class="order-status-list store-order-status-list clearfix">
			@foreach($order_status_list as $status_code => $status_code_text)
			<li class="order-status-item">
				<a href="{{ Helper::route('account_store_orders', ['status_code' => $status_code]) }}">
					<div class="order-status-item-box">
						<span class="iconfont icon-order-{{ strtolower($status_code) }}">
							<span class="number order-{{ strtolower($status_code) }}-number order_status_number" style="display: none"></span>
						</span>
					</div>
					<p>
						{{ $status_code_text }}
					</p>
				</a>
			</li>
			@endforeach
			<li class="order-status-item">
				<a href="{{ Helper::route('account_store_order_refundlist') }}">
					<div class="order-status-item-box">
						<span class="iconfont icon-order-refund">
							<span class="number order_status_number order-refund-number" style="display: none;"></span>
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
@endif
@if(!empty($store))
<div class="item-bbox">
	<div class="s-panel-header">
		<a href="{{ Helper::route('account_store_orders') }}">
			我的店铺
			<span class="to" style="position: absolute;right: 20px">></span>
		</a>
	</div>
	<div class="b-item-list">
		<div class="b-item">
			<div class="b-item-box">
				<div class="value">
					{{ isset($store['view_number']) ? $store['view_number'] : 0 }}
				</div> 
				<span class="text">浏览量</span> 
			</div>
		</div>
		<div class="b-item">
			<div class="b-item-box">
				<div class="value">
					{{ isset($store['sales_number']) ?  $store['sales_number'] : '0' }}
				</div> 
				<span class="text">总销量</span>
			</div>
		</div>
		<div class="b-item">
			<div class="b-item-box">
				<div class="value">
					{{ isset($store['wish_number']) ?  $store['wish_number'] : '0' }}
				</div>
				<span class="text">店铺收藏</span> 
			</div>
		</div>
		<div class="b-item">
			<div class="b-item-box">
				<div class="value">
					{{ isset($store['rating_honor']) ?  $store['rating_honor'] : '0' }}
				</div>
				<span class="text">店铺荣誉</span>
			</div>
		</div>
		<div class="b-item">
			<div class="b-item-box">
				<div class="value">{{ isset($store['rating']) ?  $store['rating'] : '0' }} 分 </div>
				<span class="text">店铺评分</span> 
			</div>
		</div>
		@if(!empty($store))
		<div class="b-item">
			<div class="b-item-box">
				<div class="value">
					{{ $level_text[$user['store_level']] or '' }}
				</div> 
				<span class="text">店铺级别</span>  
			</div>
		</div>
		@endif
		<div class="b-item">
			<div class="b-item-box">
				<div class="value">
					{{ $expire_date }}天@if($gift_date) (<span class="s_gift">赠</span>) @endif</span>
				</div> 
				<span class="text">剩余租期</span>
			</div>
		</div>
		<div class="b-item">
			<div class="b-item-box">
				<a href="{{ Helper::route('store_view', [$store['id']]) }}">
					<div class="value">
						查看
					</div> 
					<span class="text">店铺</span>
				</a>
			</div>
		</div>
	</div>
</div>
@endif
<!-- 我的服务 -->
<div class="my-service icon info-box">
		<div class="sec-header">
			<text>我的服务</text>
		</div>
		<div class="service-list">
			@foreach($serviceList as $key => $item)
			@if(!isset($item['is_vip']) || $user['is_vip'])
			<div class="service-list-item">
				<a href="{{ $item['url'] }}" class="{{ $item['a_class'] or '' }}">
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
<div style="padding: 10px 0px;background-color: #ffffff">
    <div style="text-align: center;padding: 40px 0px">
        <p class="self_help_qr" style="display: none;">
            <img src="{{ Helper::asset_url('/media/images/self_weiqin.jpg') }}" width="100" />
        </p>
        <p>
            <a style="color: #00f;" href="javascript:void(0)" class="js-show-help-qr">网店操作部</a>
        </p>
    </div>
</div>
<form class="upload-form store-banner-upload-form" method="post" enctype="multipart/form-data">
    <input name="image" accept="image/*" type="file" class="upload-file store-banner-file" />
</form>
<script type="text/template" id="store-expire-template">
   	<img src="{{ Helper::asset_url('/media/images/store_expire.png') }}" width="250" />
</script>
<style type="text/css">
	.layer-store-expire .layerbox-wrapper {
		box-shadow: unset;
		border: none;
	}
	.layer-store-expire .layerbox-content {
		background-color: unset;
	}
</style>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
@if($store_expire_tip)
	<script type="text/javascript">
		var store_expire_tip = 1;
	</script>
@endif
<script src="{{ Helper::asset_url('/media/scripts/view/store/store.js') }}"></script>
@endsection

