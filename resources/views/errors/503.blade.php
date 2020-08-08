@extends('layouts.app')

@section('title', '500')

@section('header_title') 500 @endsection

@section('content')
    <div class="no-results">
        <div class="result-img">@include('template.rote')</div>
        <div class="result-content">
            <p>Whoops, looks like something went wrong.</p>
        </div>
        <div class="control-group">
            <a class="btn btn-primary" href="{{ route('home') }}">{{ trans('view.words.view_other') }}</a>
        </div>
    </div>
@endsection
