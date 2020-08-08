@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('home') }}" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">名片不存在</div>
    </div>
</div>
@endsection

@section('content')
    <div class="no-results">
        <div class="result-img">@include('template.rote')</div>
        <div class="result-content">
            <p>您的能量太强大了，该页面不存在都被你找到了</p>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="{{ Helper::asset_url('media/scripts/view/search.js') }}"></script>
@endsection
