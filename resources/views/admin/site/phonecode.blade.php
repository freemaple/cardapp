@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="">网站管理</a></li>
    <li class="active">验证码</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">手机号码</label>
            <input type="text" class="form-control" name="phone" value="{{ $form['phone'] or '' }}" placeholder="请输入手机号码">
        </div>
        <select name="type" class="form-control">
            <option value="">请选择</option>
            @foreach($phonecode_type as $code_type => $code_text)
            <option value="{{ $code_type }}" @if(isset($form['type']) && $form['type'] == $code_type) selected="selected" @endif>{{ $code_text }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
       共 {{ $phonecodes->total() }} 个验证码   当前页：{{ $phonecodes->firstItem() }}-{{ $phonecodes->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>用户id</th>
                <th>手机号码</th>
                <th>验证码类型</th>
                <th>验证码</th>
                <th>ip地址</th>
                <th>是否使用</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($phonecodes))
                    @foreach($phonecodes as $key=>$list)
                        <tr>
                            <td>{{ $list['user_id'] }}</td>
                            <td>{{ $list['phone'] }}</td>
                            <td>{{ isset($phonecode_type[$list['type']]) ? $phonecode_type[$list['type']] : '' }}</td>
                            <td>{{ $list['code'] }}</td>
                            <td>{{ $list['ip'] }}</td>
                            <td>{{ $list['is_use'] == 1 ? '已使用' : '未使用' }}</td>
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
@endsection
