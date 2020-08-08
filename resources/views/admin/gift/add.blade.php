@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/gift">礼包管理</a></li>
    <li class="active">添加礼包</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation"><a href="/admin/gift">礼包</a></li>
      <li role="presentation"  class="active"><a href="/admin/gift/add">添加礼包</a></li>
    </ul>
</div>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" action="/admin/product/gift/add" method="post" id="add_gift_form">
	        <div class="form-group">
	            <input type="hidden" id="" />
                <input type="hidden" name="product_id" value="{{ $product['id'] }}" />
	        </div>
            <div class="form-group">
                 {!! csrf_field() !!}
                <label for="username" class="col-sm-2 control-label">产品名称</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control"  maxlength="50"  autocomplete="off" value="{{ $product['name'] }}" />
                  <img src="{{ $product['image'] }}" width="60" />
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">价格</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="price" maxlength="50"  autocomplete="off" placeholder="价格" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">原价</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="market_price" maxlength="50"  autocomplete="off" placeholder="原价" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">赠送礼包佣金</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="gift_commission" maxlength="50"  autocomplete="off" placeholder="赠送礼包佣金" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">赠送代购积分</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="sub_integral" maxlength="50"  autocomplete="off" placeholder="赠送代购积分" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">总经理佣金</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="director_commission" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">总监佣金</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="manager_commission" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">第一代礼包佣金（麦粒）</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="first_gift_commission_1" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">第一代礼包佣金（余额）</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="first_gift_reward_1" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">第二代礼包佣金(余额)</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="secend_gift_commission_1" maxlength="50"  autocomplete="off" placeholder="第二代礼包佣金" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">第一代礼包佣金2（麦粒）</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="first_gift_commission_2" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金2" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">钻麦第一代礼包佣金2（余额）</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="first_gift_reward_2" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金2" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">钻麦第二代礼包佣金2(余额)</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="secend_gift_commission_2" maxlength="50"  autocomplete="off" placeholder="第二代礼包佣金2" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">红利金额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="gold_amount" maxlength="50"  autocomplete="off" placeholder="红利金额" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">战友未续费扣除金麦穗到余额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="ref_remove_gold_number" maxlength="50"  autocomplete="off" placeholder="战友未续费扣除金麦穗到余额" required="required">
                </div>
            </div>
            <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">启用</label>
                <div class="col-sm-9">
                  <select class="form-control" value="" name="enable">
                    <option value="1">是</option>
                    <option value="0">否</option>
                  </select>
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
