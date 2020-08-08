@foreach($products as $pkey => $product)
<li class="product-row product-item product-item-{{ $product['id'] }}" data-id="{{ $product['id'] }}">
    <div class="product-item-box" style="position: relative;">
        <a href="{{ Helper::route('product_view', [$product['id']]) }}">
            <div class="img lazy">
                <img data-img="{{ $product['image'] or '' }}" class="lazyload" />
            </div>
            <div class="info">
                <div class="name">{{ $product['name'] or '' }}</div>
                <div class="price-info clearfix">
                    <span class="price">
                        ￥{{ $product['sku']['price'] }}
                    </span>
                    @if($product['sku']['market_price'] > 0)
                     <span class="s-price">
                        ￥{{ $product['sku']['market_price'] }}
                    </span>
                    @endif
                </div>
                <a href="javascript:void(0)" style="color: #f00;font-size: 14px" class="js-remove-wish" data-product-id="{{ $product['id'] }}">删除</a>
            </div>
            @if($product['is_self'] == '1')
            <div class="cibox" style="">
                <span class="product-cripple self-product-cripple">赏</span>
            </div>
            @else
                @if($product['integral_pay'] == '1')
                    <div class="cibox" style="">
                        <span class="product-cripple"><span class="iconfont icon-star-award"></span></span>
                    </div>
                @endif
            @endif
        </a>
    </div>
</li>
@endforeach