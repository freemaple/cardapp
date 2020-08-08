@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header_title') {{ $title }} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
	        <div class="mobile-header-back">
	            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
	        </div>
	        <div class="mobile-header-title">{{ $title }}</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="account-warp">
	<ul class="list-group">
		@foreach($doc_catalog as $key => $cate)
		<li class="list-group-item"><a href="{{ Helper::route('help_catalog_doc', [$cate['id']]) }}">{{ $cate['name'] }}
			<span class="to">></span></a>
		</li>
		@endforeach
	</ul>
</div>
@endsection
@section('scripts')
<script src="{{ Helper::asset_url('media/scripts/view/account/index.js') }}"></script>
@endsection

