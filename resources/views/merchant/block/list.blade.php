@if(!empty($merchants))
@foreach($merchants as $ckey => $merchant)
<li class="js-merchant-item js-merchant-item-{{ $merchant['id'] }} clearfix" style="background-color: #ffffff;margin-bottom: 10px">
    <a href="/store/{{ $merchant['id'] }}">
        <div class="img lazy" style="width: 100%;height: 3.55rem;overflow:hidden;">
            <img src="@if(empty($merchant['banner'])) {{ Helper::asset_url('/media/images/default_store_banner.png') }}  @else {{ HelperImage::storagePath($merchant['banner']) }} @endif" style="width: 100%;" />
        </div>
        <div class="info" style="padding: 10px">
            <div class="name clearfix">
                {{ $merchant['name'] }}  
                <a href="javascript:void(0)" style="float: right;" class="operate-btn jsLocation" data-province="{{ $merchant['provice'] }}" data-city="{{ $merchant['city'] }}" data-district="{{ $merchant['district'] }}" data-town="{{ $merchant['town'] }}" data-village="{{ $merchant['village'] }}" data-address="{{ $merchant['address'] }}" data-store-name="{{ $merchant['name'] }}" >
                导航
                </a>
            </div>
        </div>
    </a>
</li>
@endforeach
@endif