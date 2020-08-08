@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="">股权管理</a></li>
    <li class="active">配置</li>
</ul>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" action="/admin/equity/saveConfig" method="post">
            {!! csrf_field() !!}
            <div class="form-group">
                <label for="vip_equity_value" class="col-sm-2 control-label">开通vip赠送股权值</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['vip_equity_value'] }}" name="vip_equity_value"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label for="vip_equity_value" class="col-sm-2 control-label">开通vip赠送股权名额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['vip_equity_number'] }}" name="vip_equity_number"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label for="vip_equity_value" class="col-sm-2 control-label">vip直推赠送股权值</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['vip_comm_equity_value1'] }}" name="vip_comm_equity_value1"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label for="vip_equity_value" class="col-sm-2 control-label">开通店铺赠送股权值</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['store_equity_value'] }}" name="store_equity_value"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label for="vip_equity_value" class="col-sm-2 control-label">开通店铺赠送股权名额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['store_equity_number'] }}" name="store_equity_number"  autocomplete="off" >
                </div>
            </div>
            <div class="form-group">
                <label for="store_comm_equity_value1" class="col-sm-2 control-label">店铺直推赠送股权值</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  value="{{ $config['store_comm_equity_value1'] }}" name="store_comm_equity_value1"  autocomplete="off" >
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

