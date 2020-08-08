@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_card') }}">名片管理</a></li>
    <li class="active">名片列表</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">名片名称：</label>
            <input type="text" class="form-control" name="name" value="{{ $form['name'] or '' }}" placeholder="请输入名片名称">
        </div>
        <div class="form-group">
            <label class="control-label">开始时间</label>
            <input type="text" class="start_date form-control laydate-icon" name="start_date" value="{{ $form['start_date'] or '' }}"  />
        </div>
        <div class="form-group">
           <label class="control-label">结束时间</label>
            <input type="text" class="end_date form-control laydate-icon" name="end_date" value="{{ $form['end_date'] or '' }}" />
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $cards->total() }} 个名片 {{ $cards->firstItem() }}-{{ $cards->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
       <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th width="120">会员姓名/昵称</th>
                <th width="120">会员等级</th>
                <th>名片名称</th>
                <th>名片编号</th>
                <th>允许同步</th>
                <th>浏览量</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($cards))
                    @foreach($cards as $key=>$list)
                        <tr>
                            <td><a href="{{ route('admin_customer_info', $list['user_id']) }}" target="_blank">{{ $list->fullname ? $list->fullname : $list->nickname }} ({{ $list->phone ? $list->phone : $list->phone }})</a></td>
                            <td>
                                {{ $level_status[$list->level_status] or '' }} 
                                <p>注册时间：{{ $list->created_at or '' }}</p>
                            </td>
                            <td title="{{ $list['name'] }}"><a href="{{ route('card_view', $list['card_number']) }}" target="_blank">{{ $list['name'] }}</a></td>
                            <th>{{ $list['card_number'] }}</th>
                            <td title="允许同步">{{ $list->is_allow_sync == '1' ? '允许' : '不允许' }}</td>
                            <td title="浏览量">
                                {{ $list->view_number  }}
                            </td>
                            <td>{{ $list->created_at }}</td>
                            <td>
                                <a type="button" class="js_set_syn"  href="javascript:void(0)" data-id="{{ $list['id'] }}">
                                    @if($list['is_allow_sync'] != '1') 允许同步 @else 取消允许同步 @endif
                                </a>
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
    $(".js_set_syn").on('click', function(){
        var card_id = $(this).attr('data-id');
        $.post('/admin/card/set_syn', {'card_id': card_id}, function(result){
            if(result.code == '200'){
                window.location.reload();
            } else if(result.message != ''){
                $.showMessage(result.message);
            }
        });
    });
</script>
@endsection
