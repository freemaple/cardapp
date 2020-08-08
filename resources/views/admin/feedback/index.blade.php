@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_feedback') }}">反馈管理</a></li>
    <li class="active">反馈列表</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">姓名：</label>
            <input type="text" class="form-control" name="fullname" value="{{ $form['fullname'] or '' }}" placeholder="请输入姓名">
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">手机号码：</label>
            <input type="text" class="form-control" name="phone" value="{{ $form['phone'] or '' }}" placeholder="请输入手机号码">
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">状态</label>
            <select name="status">
                <option value="">全部</option>
                <option value="0" @if(isset($form['status']) && $form['status'] === '0') selected="selected" @endif>待处理</option>
                <option value="1" @if(isset($form['status']) && $form['status'] == '1') selected="selected" @endif>已处理</option>
            </select>
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
        共 {{ $feedbacks->total() }} 个反馈 {{ $feedbacks->firstItem() }}-{{ $feedbacks->lastItem() }}
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
                <th>姓名</th>
                <th>手机号码</th>
                <th>内容</th>
                <th>提交时间</th>
                <th>处理时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($feedbacks))
                    @foreach($feedbacks as $key=>$list)
                        <tr>
                            <td>{{ $list->user_id }}</td>
                            <td>{{ $list->fullname  }}</td>
                            <td>{{ $list->phone }}</td>
                            <td>{{ $list->content }}</td>
                            <td>{{ $list->created_at }}</td>
                            <td>{{ $list->hander_time or '' }}</td>
                            <td>
                                @if($list['status'] != '1')
                                <a type="button"  class="btn btn-danger js_feedback_hander"  href="javascript:void(0)" data-id="{{ $list['id'] }}">
                                    标记已处理
                                </a>
                                @endif
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
    $(".js_feedback_hander").on('click', function(){
        var feedback_id = $(this).attr('data-id');
        $.post('/admin/feedback/hander', {'feedback_id': feedback_id}, function(result){
            if(result.code == '200'){
                window.location.reload();
            } else if(result.message != ''){
                $.showMessage(result.message);
            }
        });
    });
</script>
@endsection
