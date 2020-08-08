@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">会员管理：{{ $user['fullname'] }} ({{ $user['fullname'] }}) ￥{{ !empty($user->reward_info) ?  $user->reward_info['amount'] : 0 }}</a></li>
    <li class="active">赏金明细</li>
</ul>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $reward_records->total() }} 个赏金明细 {{ $reward_records->firstItem() }}-{{ $reward_records->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>会员id</th>
                <th>收入类型</th>
                <th>金额</th>
                <th>内容</th>
                <th>备注</th>
                <th>充值订单id</th>
                <th>创建时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($reward_records))
                    @foreach($reward_records as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->user_id }}
                            </td>
                            <td>
                                {{ $list->type == '1' ? '+' : '-' }}
                            </td>
                            <td>
                                 {{ $list->amount }}
                            </td>
                            <td>
                                {{ $list->content }}
                            </td>
                            <td>
                                {{ $list->remarks }}
                            </td>
                            <td>
                                {{ $list->order_id }}
                            </td>
                            <td>
                                {{ $list->created_at }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>  
@if(!empty($pager))
<div class="panel-body">
    <div class="text-center">{{ $pager }}</div>
</div>
@endif
@endsection
@section('scripts')
<script type="text/javascript">
    
</script>
@endsection
