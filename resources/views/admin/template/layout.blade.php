<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width" />
        <title>@section('title') {{ $title or '' }}@show</title>
        @foreach(\App\Assets\Admin::styles() as $style)
        <link type="text/css" href="{{ $style }}" rel="stylesheet" />
        @endforeach
        @yield('styles')
    </head>
    <body>
    	<div class="container main_container">
           @yield('header', \App\Block\Admin::header())
		   <div class="main_content">
		   		@include('admin.template.message')
		        @yield('content')
		    </div>
	    </div>
	    @include('admin.template.footer')
        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
        @foreach(\App\Assets\Admin::scripts() as $script)
        <script type="text/javascript" src="{{ $script }}"></script>
        @endforeach
        @yield('scripts')
    </body>
</html>
