@extends('layouts.app')

@section('header')@endsection

@section('content')
	<div class="no-results">
        <div class="result-img">@include('template.rote')</div>
	    <div class="result-content">
	        <p>产品已下架或者不存在</p>
	    </div>
	    <div class="control-group">
	        <a class="btn btn-primary" href="{{ Helper::route('home') }}">查看其他</a>
	    </div>
	</div>
@endsection