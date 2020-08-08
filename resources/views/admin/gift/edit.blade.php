@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/product/gift">礼包管理</a></li>
    <li class="active">编辑礼包</li>
</ul>
<div class="well">
    <fieldset>
        <legend>基础信息</legend>
        <form class="form-horizontal" action="/admin/product/gift/edit" method="post" id="add_gift_form">
	        <div class="form-group">
                <input type="hidden" name="id" value="{{ $gift['id'] }}" />
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
                <label for="username" class="col-sm-2 control-label">活动实卖价格</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="price" maxlength="50"  autocomplete="off" placeholder="价格" required="required" value="{{ $gift['price'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">原价</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="market_price" maxlength="50"  autocomplete="off" placeholder="原价" required="required" value="{{ $gift['market_price'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">自动赠送礼包麦粒</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="gift_commission" maxlength="50"  autocomplete="off" placeholder="赠送礼包佣金" required="required" value="{{ $gift['gift_commission'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">自动赠送代购积分</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="sub_integral" maxlength="50"  autocomplete="off" placeholder="赠送代购积分" required="required" value="{{ $gift['sub_integral'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">总经理佣金</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="director_commission" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金" required="required" value="{{ $gift['director_commission'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">总监佣金</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="manager_commission" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金" required="required" value="{{ $gift['manager_commission'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">第一代礼包佣金（麦粒）</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="first_gift_commission_1" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金（金麦）" required="required" value="{{ $gift['first_gift_commission_1'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">第一代礼包佣金（余额）</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="first_gift_reward_1" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金（余额" required="required" value="{{ $gift['first_gift_reward_1'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">第二代礼包佣金（余额）</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="secend_gift_commission_1" maxlength="50"  autocomplete="off" placeholder="第二代礼包佣金" required="required" value="{{ $gift['secend_gift_commission_1'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">钻麦第一代礼包佣金2（麦粒）</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="first_gift_commission_2" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金2" required="required" value="{{ $gift['first_gift_commission_2'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">钻麦第一代礼包佣金2（余额）</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="first_gift_reward_2" maxlength="50"  autocomplete="off" placeholder="第一代礼包佣金（余额" required="required" value="{{ $gift['first_gift_reward_2'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">钻麦第二代礼包佣金（余额）2</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="secend_gift_commission_2" maxlength="50"  autocomplete="off" placeholder="第二代礼包佣金2" required="required" value="{{ $gift['secend_gift_commission_2'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">红利金额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="gold_amount" maxlength="50"  autocomplete="off" placeholder="红利金额" required="required" value="{{ $gift['gold_amount'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="username" class="col-sm-2 control-label">战友未续费扣除金麦穗到余额</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" name="ref_remove_gold_number" maxlength="50"  autocomplete="off" placeholder="战友未续费扣除金麦穗到余额" required="required" value="{{ $gift['ref_remove_gold_number'] }}">
                </div>
            </div>
            <div class="form-group">
                <label for="isdisabled" class="col-sm-2 control-label">启用</label>
                <div class="col-sm-9">
                  <select class="form-control" value="{{ $gift['enable'] }}" name="enable">
                    <option value="1" @if($gift['enable'] == '1') selected="selected" @endif>是</option>
                    <option value="0"  @if($gift['enable'] == '0') selected="selected" @endif>否</option>
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
