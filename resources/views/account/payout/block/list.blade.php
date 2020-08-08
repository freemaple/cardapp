@foreach($payoutApplys as $ckey => $payoutApply)
<li class="list-item clearfix">
	<div class="list-item-header">
		账单号: {{ $payoutApply['number'] }}
		<span class="list-status-text">
            @if($payoutApply['status'] == '2')
            <span class="text-info" style="font-size: 14px;">{{ $payout_status[$payoutApply['status']] }}</span>
            @else
                {{ $payout_status[$payoutApply['status']] }}
            @endif
        </span>
	</div>
    <div class="list-item-content clearfix">
    	<div class="name">申请金额：<span class="text-info" style="font-size: 14px;">￥{{ $payoutApply['amount'] }}</span></div>
        <div class="name">实际到帐金额：<span class="text-info" style="font-size: 14px;">￥{{ $payoutApply['actual_amount'] }}</span></div>
        <div class="name"></div>
        <div class="name">真实姓名: {{ $payoutApply['fullname'] }}</div>
        <div class="name">支付宝: {{ $payoutApply['alipay'] }}</div>
        <div class="name">申请时间：{{ $payoutApply['created_at'] }}</div>
        @if(!empty($payoutApply['approve_time']))
        <div class="text-red">处理时间: {{ $payoutApply['approve_time'] }}</div>
        @endif
        @if($payoutApply['remarks'] != '')
        <div class="text-red">备注：{{ $payoutApply['remarks'] }}</div>
        @endif
    </div>
</li>
@endforeach