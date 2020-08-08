@extends('layouts.app')

@section('title') {{ $title }}  @endsection

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
	@if(!empty($order_products))
	<form name="order-review" class="order-review" data-action="/api/order/reviews/add/{{ $order_id }}">
		<div class="order-product-review-list">
			@foreach($order_products as $o => $o_p)
			<div class="order-product-review-item order-product-review-{{ $o_p['order_product_id'] }}">
				<div class="order-product-item clearfix">
					<div class="img">
						<a href="{{ Helper::route('product_view', $o_p['product_id']) }}"><img src="{{ $o_p['image'] or '' }}" /></a>
					</div>
					<div class="info">
						<div class="info-box">
							<div><a href="{{ Helper::route('product_view', $o_p['product_id']) }}">{{ $o_p['product_name'] }}"</a></div>
							<div class="price-info">
								{{ $o_p['price'] }} * {{ $o_p['quantity'] }}
	                    	</div>
	                    	@if(!empty($o_p['spec']))
	                        <div class="spec">
	                           <span>{{ $o_p['spec'] }}
	                        </div>
	                        @endif
						</div>
					</div>
				</div>
				<div class="order-review-box">
					<div class="form-group">
						<span class="form-label">评分</span>
						<span class="rating-box clearfix">
							<ul class="rating-star js-rating-star" data-id="{{ $o_p['order_product_id'] }}">
								<li class="select"></li>
								<li class="select"></li>
								<li class="select"></li>
								<li class="select"></li>
								<li class="select"></li>
							</ul>
						</span>
					</div>
					<div class="form-group">
						<textarea rows="6" class="form-control review_text" name="review_text[]" placeholder="评论内容" maxlength="300" required="required"></textarea>
						<div class="errormsg" style="display: none"></div>
					</div>
					<div class="upload-image-box">
						<a class="js-add-review-image" data-id="{{ $o_p['order_product_id'] }}">
							<span class="iconfont icon-photo"></span>
						</a>
						<div class="upload-image-list"></div>
						<div class="upload-image-file-list" style="visibility: hidden;">
							<input accept="image/jpeg,image/jpg,image/png" type="file" class="upload-image-file" name="review_image_{{ $o_p['order_product_id'] }}[]" data-id="{{ $o_p['order_product_id'] }}" data-name="review_image_{{ $o_p['order_product_id'] }}[]" />
							<input accept="image/jpeg,image/jpg,image/png" type="file" class="upload-image-file" name="review_image_{{ $o_p['order_product_id'] }}[]" data-id="{{ $o_p['order_product_id'] }}" data-name="review_image_{{ $o_p['order_product_id'] }}[]" />
							<input accept="image/jpeg,image/jpg,image/png" type="file" class="upload-image-file" name="review_image_{{ $o_p['order_product_id'] }}[]" data-id="{{ $o_p['order_product_id'] }}" data-name="review_image_{{ $o_p['order_product_id'] }}[]" />
						</div>
					</div>
				</div>
				<input type="hidden" name="product_id[]" class="product_id" value="{{ $o_p['product_id'] }}" />
				<input type="hidden" name="review_rate[]" class="review_rate_value" value="5" />
				<input type="hidden" name="order_product_id[]" class="order_product_id" value="{{ $o_p['order_product_id'] }}" />
			</div>
			@endforeach
		</div>
	</form>
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
@if(!empty($order_products))
@section('footer')
<div class="mobile-footer">
   <a href="javascript:void(0)" class="btn btn-primary btn-block js-submit-order-review">提交评论</a>
</div>
@endsection
@endif
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/order/review.js') }}"></script>
@endsection

