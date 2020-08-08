 @foreach($message_list as $ckey => $message)
    <li class="list-item js-message-item js-message-item-{{ $message['id'] }} clearfix">
        <div class="list-item-content clearfix">
            <div class="name" style="color: #00bcd4">{{ $message['name'] }}</div>
            <div class="content" style="color: #999999;margin: 5px 0px">{{ $message['content'] }}</div>
            @if($message['link'] != '')
                @if($message['order_no'] != '')
                <div><a class="text-red" href="{{ $message['link'] }}">
                @if($message['message_type'] == 'order_refund')
                    去处理退换货
                @else
                查看订单
                @endif
                </a>
                </div>
                @else
                <div><a class="text-red" href="{{ $message['link'] }}">进入查看</a></div>
                @endif
            @endif
            <div class="name" style="margin-top: 5px">时间：{{ $message['created_at'] }}</div>
        </div>
    </li>
@endforeach