@extends('layouts.app')

@section('header')@endsection

@section('content')
	@include('auth.block.login')
@endsection

@section('copyright', view('template.copyright'))
