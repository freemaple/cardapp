@extends('layouts.app')

@section('title')@lang('meta.user.index.title')@endsection

@section('header_title') 收入明细 @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	<div class="mobile-header-back">
                <a href="/account/wallet"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">收入明细</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="account-warp">
	<div class="">
		<div class="wallet-records-list-box js-wallet-records-list-box" data-action="/api/wallet/record" data-page="1" @if($walletRecord['last_page'] == 1) data-load-more="-1" @endif>
			<ul class="clearfix rf-list js-wallet-records-list">
	        	@include('account.wallet.block.list', ['walletRecords' => $walletRecord['data']])
    		</ul>
    		<div class="waiting-load-block js-load-block" style="display: none">
            	<div class="waiting-loading"></div>
            	<div class="text">Loading...</div>
        	</div>
    	</div>
	</div>
</div>
@endsection
@section('footer')
	@include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/wallet.js') }}"></script>
@endsection

