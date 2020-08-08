@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">金麦管理</a></li>
    <li class="active">金麦</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label">姓名</label>
            <input type="text" class="form-control" name="fullname" value="{{ $form['fullname'] or '' }}"  />
        </div>
        <div class="form-group">
            <label class="control-label">发放日期</label>
            <input type="text" class="start_date1 form-control laydate-icon" name="date" value="{{ $form['date'] or '' }}"  />
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($goldDayConfig))
<div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">发放详情</h3>
    </div>
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>日期</th>
                <th>实际红利余额</th>
                <th>实际应发金额</th>
                <th>实际发放金麦数量</th>
                <th>发放时间</th>
                <th>发放状态</th>
                <th>创建时间</th>
                <th>更新时间</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        {{ $goldDayConfig->date }}
                    </td>
                    <td>
                        ￥{{ $goldDayConfig->bouns_amount }}
                    </td>
                    <td>
                        ￥{{ $goldDayConfig->should_issued_amount }}
                    </td>
                    <td>
                        {{ $goldDayConfig->gold_number }}
                    </td>
                    <td>
                        {{ $goldDayConfig->handle_time }}
                    </td>
                    <td>
                        {{ $goldDayConfig->status == '1' ? '已发放' : '未发放' }}
                    </td>
                    <td>
                        {{ $goldDayConfig->created_at }}
                    </td>
                    <td>
                        {{ $goldDayConfig->updated_at }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endif  
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $userGoldDays->total() }} 个记录 {{ $userGoldDays->firstItem() }}-{{ $userGoldDays->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-heading">
      <h3 class="panel-title">发放记录</h3>
    </div>
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>会员id</th>
                <th>手机号码</th>
                <th>日期</th>
                <th>红利金额</th>
                <th>发放时间</th>
                <th>更新时间</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($userGoldDays))
                    @foreach($userGoldDays as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->user_id }}
                                <a type="button" class="text-info"  href="/admin/customer/{{ $list['user_id'] }}" target="_blank">
                                    详情
                                </a>
                            </td>
                            <td>
                                <p>
                                    <a href="{{ HelperImage::getavatar($list->avatar) }}" target="_blank"><img src="{{ HelperImage::getavatar($list->avatar) }}" width="40"></a>
                                </p>
                                {{ $list->phone }}
                                ({{ $list->fullname ? $list->fullname : $list->nickname }})
                            </td>
                            <td>
                                {{ $list->date }}
                            </td>
                            <td>
                                ￥{{ $list->bonus_amount }}
                            </td>
                            <td>
                                {{ $list->created_at }}
                            </td>
                            <td>
                                {{ $list->updated_at }}
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

