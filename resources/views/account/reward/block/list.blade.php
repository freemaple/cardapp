@foreach($recordRecords as $ckey => $r)
<li class="list-item clearfix" style="padding: 2px 10px;margin-bottom: 2px;margin-top: 0px">
    <div class="list-item-content clearfix" style="padding: 2px 10px;margin-bottom: 2px;margin-top: 0px">
        <div class="name">{{ $r['content'] }}</div>
        <div style="margin-top: 5px;color: #999">
        	{{ $r['created_at'] }}
        </div>
        <div style="position: absolute;right: 20px;top: 50%;margin-top: -10px">
        	<span class="list-status-text" style="color: #f00;font-size: 16px">@if($r['type'] == '1') + @else - @endif ￥{{ $r['amount'] }}</span>
        </div>
    </div>
</li>
@endforeach