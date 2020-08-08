@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">会员管理</a></li>
    <li class="active">积分明细</li>
</ul>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $integral_records->total() }} 个积分明细 {{ $integral_records->firstItem() }}-{{ $integral_records->lastItem() }}
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
                <th>会员类型</th>
                <th>积分</th>
                <th>内容</th>
                <th>备注</th>
                <th>充值订单id</th>
                <th>创建时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($integral_records))
                    @foreach($integral_records as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->user_id }}
                            </td>
                            <td>
                                {{ $list->type }}
                            </td>
                            <td>
                                {{ $list->point }}
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
