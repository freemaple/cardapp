@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	    	 <div class="mobile-header-back">
                <a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	        <div class="mobile-header-right">
                <a class="btn btn-info js-confirm-icon" href="{{ Helper::route('account_payout_apply') }}" style="height: 34px;line-height: 34px">我要提现</a>
            </div>
	    </div>
	</div>
@endsection

@section('content')
<div class="account-warp">
	@if(!empty($payoutApplys) && count($payoutApplys) > 0)
	<div class="">
		<div class="js-payout-apply-list-box"  data-page="1" data-action="/api/payout/apply/list">
			<ul class="clearfix js-payout-apply-list">
				@include('account.payout.block.list', ['payoutApplys' => $payoutApplys])
			</ul>
			<div class="waiting-load-block js-load-block" style="display: none">
		    	<div class="lds-css ng-scope"><div class="lds-rolling"><div></div></div>
			</div>
		</div>
	</div>
	@else
		<div class="no-results">
            <div class="result-img">@include('template.rote')</div>
            <div class="result-content">
                <p>我王是土豪，不缺钱！都没提现过！</p>
            </div>
        </div>
	@endif
</div>
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/payout.js') }}"></script>
@endsection

