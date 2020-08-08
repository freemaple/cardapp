@foreach($walletRecords as $ckey => $wr)
<li class="list-item js-rf-item js-rf-item-{{ $wr['id'] }} clearfix">
	<div class="list-item-header clearfix">
        <span class="list-no">{{ $wr['title'] }}</span>
        <span class="list-status-text">@if($wr['type'] == '1') 收入 @else 支出 @endif ￥{{ $wr['amount'] }}</span>
    </div>
    <div class="list-item-content clearfix">
        <div class="name">{{ $wr['content'] }}</div>
        <div>
        	{{ $wr['created_at'] }}
        </div>
    </div>
</li>
@endforeach