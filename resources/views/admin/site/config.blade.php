@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="">站点管理</a></li>
    <li class="active">配置</li>
</ul>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" action="/admin/site/saveConfig" method="post">
            {!! csrf_field() !!}
            <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">开启开通网店自动赠送积分</label>
                <div class="col-sm-9">
                  <select class="form-control" value="{{ $config['store_integral_send_open'] }}" name="store_integral_send_open">
                    <option value="1" @if($config['store_integral_send_open'] == "1") selected="selected" @endif>是</option>
                    <option value="0" @if($config['store_integral_send_open'] != "1") selected="selected" @endif>否</option>
                  </select>
                </div>
            </div>
            <div class="form-group">
                <label for="store_integral_send_amount" class="col-sm-2 control-label">开通网店自动赠送积分金额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['store_integral_send_amount'] }}" name="store_integral_send_amount"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">开启开通VIP自动赠送积分</label>
                <div class="col-sm-9">
                  <select class="form-control" value="{{ $config['vip_integral_send_open'] }}" name="vip_integral_send_open">
                    <option value="1" @if($config['vip_integral_send_open'] == "1") selected="selected" @endif>是</option>
                    <option value="0" @if($config['vip_integral_send_open'] != "1") selected="selected" @endif>否</option>
                  </select>
                </div>
            </div>
            <div class="form-group">
                <label for="password" class="col-sm-2 control-label">开通VIP自动赠送积分金额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['vip_integral_send_amount'] }}" name="vip_integral_send_amount"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <div class="text-center">
                  <input type="submit" class="btn btn-primary" value="保存" />
                </div>
            </div>
        </form>
    </fieldset>
</div>
@endsection
@section('scripts')
{!! App\Assets\Admin::script('admin/scripts/module/admin.js') !!}
@endsection

