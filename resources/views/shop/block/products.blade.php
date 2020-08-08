@if(!empty($products))
@foreach($products as $pkey => $product)
<li class="product-row product-item product-item-{{ $product['id'] }}" data-id="{{ $product['id'] }}">
    <div class="product-item-box">
        <a href="{{ Helper::route('product_view', [$product['id']]) }}">
            <div class="img lazy">
                <img class="lazyload" data-img="{{ $product['image'] or '' }}" />
            </div>
            <div class="info">
                <div class="name">{{ $product['name'] or '' }}</div>
                <div class="price-info clearfix">
                    <span class="price">
                        ￥{{ $product['sku']['price'] or '' }}
                    </span>
                    @if($product['sku']['market_price'] > 0)
                     <span class="s-price">
                        ￥{{ $product['sku']['market_price'] or '' }}
                    </span>
                    @endif
                </div>
                <div class="sp-box">
                    @if(isset($product['sku']['share_integral']) && $product['sku']['share_integral'] > 0)
                        <span class="sp-btn share-sp-btn">自购/分享赚<span>￥{{ $product['share_amount_min']  }}~{{ $product['share_amount_max']  }}</span> 红包</span>
                    @else
                        @if(!empty($product['store_id']))
                        <span class="sp-btn js_to_link_href" data-href="{{ Helper::route('store_view', $product['store_id']) }}">{{ $product['store_name'] }}</</span>
                        @else
                        <span class="sp-btn buy-sp-btn">{{ $product['store_name'] }}</span>
                        @endif
                    @endif
                </div>
            </div>
            @if(isset($product['is_self']) && $product['is_self'] == '1')
            <div class="cibox" style="">
                <span class="product-cripple self-product-cripple">赏</span>
            </div>
            @else
                @if(isset($product['integral_pay']) && $product['integral_pay'] == '1')
                    <div class="cibox" style="">
                        <span class="product-cripple"><span class="iconfont icon-star-award" style="color: #ffffff"></span></span>
                    </div>
                @endif
            @endif
        </a>
    </div>
</li>
@endforeach
@endif