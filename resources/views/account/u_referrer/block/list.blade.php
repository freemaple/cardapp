@foreach($referrer_list as $ckey => $rf)
<li class="list-item js-rf-item js-rf-item-{{ $rf['id'] }} clearfix">
    <div class="list-item-content clearfix">
        <div class="name" style="color: #00bcd4">{{ $rf['fullname'] ? $rf['fullname'] : $rf['nickname'] }}</div>
        <div class="name clearfix">电话号码：{{ $rf['phone'] }} <span style="padding: 0px 5px;float: right;"><a href="tel:{{ $rf['phone'] }}" class="pup-a-tel ballon"><span class="iconfont icon-phone"></span></a></span>
        </div>
        <div class="name clearfix" style="margin-top: 10px">
        	注册时间：{{ $rf['created_at'] }}
        	@if($rf['rupgrade'] == '1')
                <a href="{{ $rf['rupgrade_link'] }}" class="text-info" style="float: right">为他代购</a>
            @endif
        </div>
    </div>
</li>
@endforeach