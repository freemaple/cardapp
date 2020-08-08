@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="{{ Helper::route('account_orders') }}"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">{{ $title }}</div>
    </div>
</div>
@endsection
@section('content')
<div class="order-review-warp">
	@if(!empty($order_reviews))
	<div class="order-product-review-list">
		@foreach($order_reviews as $o => $o_r)
		<div class="order-product-review-item order-product-review-{{ $o_r['order_product_id'] }}">
			<div class="order-product-item clearfix">
				<div class="img">
					<a href="{{ Helper::route('product_view', $o_r['product_id']) }}"><img src="{{ $o_r['sku_image'] or '' }}" /></a>
				</div>
				<div class="info">
					<div class="info-box">
						<div class="name"><a href="{{ Helper::route('product_view', $o_r['product_id']) }}">{{ $o_r['product_name'] or '' }}</a></div>
						<div class="rating-info">
							<div class="rating-box">
								<ul class="rating-star clearfix">
									@for($i = 1; $i<=5; $i++)
									<li class="@if($i<=$o_r['review_rate'])select @endif"></li>
									@endfor
								</ul>
							</div>
							<span>{{ $o_r['review_rate'] }}</span>
						</div>
						<div class="rating-text">
							{{ $o_r['review_text'] }}
						</div>
						@if(!empty($o_r['reviews_image']))
						<div class="order-review-image-list">
							@foreach($o_r['reviews_image'] as $rikey => $r_img)
							<span class="order-review-image-item"><a href="{{ $r_img }}" target="_blank"><img src="{{ $r_img }}" /></a></span>
							@endforeach
						</div>
						@endif
						@if(isset($o_r['reply_text']) && $o_r['reply_text'] != '')
						<div class="reply-text">
							<span>Reply:</span>{{ $o_r['reply_text'] }}
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
		@endforeach
	</div>
	@else
	<div class="no-results">
	    <div class="result-img">@include('template.rote')</div>
	    <div class="result-content">
	        @if(!empty($message))
	            <p>{{ $message }}</p>
	        @else
	            <p>{{ trans('view.site.no_find_any_results') }}</p>
	        @endif
	    </div>
	</div>
	@endif
</div>
@endsection
@if(!empty($order_product))
@endif

