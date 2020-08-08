@foreach($products as $pkey => $product)
<li>
    <div class="item-box">
        <a href="{{ Helper::route('product_view', $product['id']) }}">
            <div class="img lazy">
                <img src="{{ $product['image'] or '' }}" />
            </div>
            <div class="price">￥{{ $product['sku']['price'] or '' }}</div>
            @if($product['sku']['market_price'] > 0)
            <div class="sprice">￥{{ $product['sku']['market_price'] or '' }}</div>
            @endif
        </a>
    </div>
</li>
@endforeach