@foreach($referrer_list as $ckey => $rf)
<li class="list-item js-rf-item js-rf-item-{{ $rf['id'] }} clearfix" style="margin-top: 10px;padding: 5px 10px">
    <div class="list-item-content clearfix">
    	<div class="img" style="width: 60px;height: 60px;overflow: hidden;min-height: unset;">
            <span class="lazy"><img style="width: 60px;height: 60px" data-img="{{ HelperImage::getavatar($rf['avatar']) }}" class="lazyload"  /></span>
        </div>
        <div class="info">
            <div class="info-box" style="padding-left: 80px">
                <div class="name" style="line-height: 14px">
                    {{ $rf['fullname'] ? $rf['fullname'] : $rf['nickname'] }}
                    <span style="font-size: 14px;color: #fe7589;border: 1px solid #fe7589;padding:2px 2px;border-radius: 5px;margin: 0px 5px">{{ $rf['level_status_text'] }}</span>
                    @if($rf['store_level']>= 1)
                    <span style="font-size: 14px;color: #00f;border: 1px solid #fe7589;padding:2px 2px;border-radius: 5px;margin: 0px 5px">{{ $store_level_text[$rf['store_level']] or '' }}</span>
                    @endif
                </div>
                @if(!empty($rf['phone']))
                <div class="name" style="line-height: 14px">
                    电话号码：{{ $rf['phone'] }} 
                    <span style="padding: 0px 5px;"><a href="tel:{{ $rf['phone'] }}" class="pup-a-tel ballon" style="border:none"><span class="iconfont icon-phone" style="font-size: 20px;"></span></a></span>
                </div>
                @endif
                <div class="name" style="line-height: 14px">注册时间：{{ $rf['created_at'] }}
                    @if(!empty($rf['weixin_qr']))
                    <span style=""><a href="javascript:void(0)" class="js-show-weixin" data-weixin-qr="{{ HelperImage::storagePath($rf['weixin_qr']) }}"><span style="font-size: 16px;color: #fe7589;border-radius: 5px;margin-left: 10px;border: 1px solid #fe7589;padding: 2px">加微信</span></a></span>
                    @endif
                </div>
                <div class="name" style="margin-top: 5px">到期时间: {{ $rf['vip_end_date'] }} </div>
                <div class="name" style="margin: 5px 0px 5px 0px;line-height: 14px"><span style="font-size: 14px;margin-right: 10px"><span class="iconfont icon-star-award" style="color: #fe7589;margin-right: 5px"></span><span style="color: #fe7589;">{{ $rf['rf_count'] }}</span></span> <span style="font-size: 14px;">当月：<span style="color: #fe7589;">{{ $rf['rf_month_count'] }}</span></span></div>
                <div class="name clearfix">
                    荣誉功勋：{{ $rf['honor_value'] }} ：{{ $rf['honor_vip_value'] }} 
                    @if($rf['rupgrade'] == '1')
                    <a href="{{ $rf['rupgrade_link'] }}" class="text-info" style="float: right">为他代购</a>
                    @endif
                </div>
            </div>
           
        </div>
    </div>
</li>
@endforeach