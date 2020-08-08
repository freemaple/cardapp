@extends('layouts.app')

@section('header_title') {{ $title }} @endsection

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
<div class="account-warp">
    <div class="">
        @if($message_list->total() == 0)
        <div class="no-results">
            <div class="result-img">@include('template.rote')</div>
            <div class="result-content">
                <p>没有任何奏折！皇上该打江山啦！再不奋斗就老啦！</p>
            </div>
        </div>
        @else
            <div class="message-list-box js-message-list-box" data-action="/api/messages/list" data-page="1">
                <ul class="clearfix rf-list js-message-list">
                    @include('account.message.block.list', ['message_list' => $message_list])
                </ul>
                <div class="waiting-load-block js-load-block" style="display: none">
                    <div class="lds-css ng-scope"><div  class="lds-rolling"><div></div></div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
@section('footer')
    @include('account.block.footer', ['current_menu' =>'index'])
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/messages.js') }}"></script>
@endsection

