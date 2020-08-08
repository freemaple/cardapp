@extends('layouts.app')

@section('header_title') {{ $catalog['name']}} @endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">{{ $catalog['name'] }}</div>
	    </div>
	</div>
@endsection

@section('content')
@if(!empty($doc_list) && count($doc_list))
<div class="">
    <ul class="list-group">
        @include('doc.block.list', ['doc_list' => $doc_list])
    </ul>
</div>
@else
    <div class="no-results">
        <div class="result-img">
            <div class="result-img">@include('template.rote')</div>
        </div>
    </div>
@endif
@endsection




