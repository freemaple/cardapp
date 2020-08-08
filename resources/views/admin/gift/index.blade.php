@extends('admin.template.layout')
@section('styles')
<style type="text/css">
    .product_image_item {
        display: inline-block;
    }
</style>
@endsection
@section('content')
<ul class="breadcrumb">
    <li>产品管理</li>
    <li class="active">产品列表</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
      <li role="presentation"  class="active"><a href="/admin/product?is_self=1&is_add_gift=1">添加礼包</a></li>
    </ul>
</div>
<div class="panel panel-info">
    <div class="well">
        <form class="form-inline" role="form">
            <div class="form-group">
                <label class="control-label" for="name">名称</label>
                <input type="text" class="form-control" name="name" value="{{ $form['name'] or '' }}" size="10" placeholder="请输入名称">
            </div>
        </form>
    </div>
    @if(!empty($pager))
        <div class="clearfix pager_block">
            <div class="item_status pull-left">
                共 {{ $gift_list['total'] }} 个产品，当前 {{ $gift_list['from'] }}-{{ $gift_list['to'] }}
            </div>
            <div class="pull-right pager_box">{{ $pager }}</div>
        </div>
    @endif
    <div class="panel-body">
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" class="rows_check" /></th>
                <th>编号</th>
                <th width="80">产品图片</th>
                <th width="100">礼包价格</th>
                <th width="60">库存</th>
                <th width="60">赠送礼包佣金</th>
                <th width="60">赠送代购积分</th>
                <th width="80">总经理(佣金）</th>
                <th width="80">总监(佣金）</th>
                <th width="80">第一代礼包佣金(麦粒）</th>
                <th width="80">第一代礼包佣金（余额)</th>
                <th width="80">第二代礼包佣金(余额)</th>
                <th width="80">钻麦第一代礼包佣金2 (麦粒）</th>
                 <th width="80">钻麦第一代礼包佣金2（余额)</th>
                <th width="80">钻麦第二代礼包佣金(余额）2</th>
                <th width="60">红利金额</th>
                <th width="100">是否启用</th>
                <th>创建人</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($gift_list['data']))
                @foreach($gift_list['data'] as $key => $list)
                    <tr>
                       <td><input type="checkbox" class="row_check" data-id="{{ $list['id'] }}" /></td>
                       <td>{{ $list['id'] }}</td>
                       <td>
                          <img src="{{ $list['image']}}" width="80" />
                          <p>{{ $list['product']['name'] }}</p>
                       </td>
                       <td style="color: #f00">￥{{ $list['price'] }} <span style="text-decoration: line-through;color: #999999">￥{{ $list['market_price'] }}</span></td>
                       <td @if($list['skus_stock'] <=0) style="color: #f00;font-size: 18px" @endif>{{ $list['skus_stock'] }}</td>
                       <td>{{ $list['gift_commission'] }}</td>
                       <td>{{ $list['sub_integral'] }}</td>
                       <td>{{ $list['director_commission'] }}</td>
                       <td>{{ $list['manager_commission'] }}</td>
                       <td>{{ $list['first_gift_commission_1'] }}</td>
                       <td>{{ $list['first_gift_reward_1'] }}</td>
                       <td>{{ $list['first_gift_commission_2'] }}</td>
                       <td>{{ $list['first_gift_reward_2'] }}</td>
                       <td>{{ $list['secend_gift_commission_1'] }}</td>
                       <td>{{ $list['secend_gift_commission_2'] }}</td>
                       <td>{{ $list['gold_amount'] }}</td>
                       <td>{{ $list['enable'] == '1' ? '启用' : '禁用' }}</td>
                       <td>{{ $list['admin_name'] }}</td>
                       <td>{{ $list['created_at'] }}</td>
                       <td>
                         <a type="button" class="btn btn-primary"  href="/admin/product/gift/edit?id={{ $list['id'] }}">
                                编辑
                            </a>
                       </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        @if(!empty($pager))
        <div class="text-center">{{ $pager }}</div>
        @endif
    </div>
</div>
@endsection