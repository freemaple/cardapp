@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_store') }}">店铺管理</a></li>
    <li class="active">店铺列表</li>
</ul>
@if(empty($form['pa']) || $form['pa'] != 'approval')
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation" class="@if(empty($form['order_status_code']))active @endif">
            <a href="{{ route('admin_store') }}">全部</a>
        </li>
        <li role="presentation" class="@if(empty($form['order_status_code']))active @endif">
            <a href="{{ route('admin_store', ['store_expires_status' => '0']) }}">待续约临时店铺</a>
        </li>
        <li role="presentation" class="@if(empty($form['order_status_code']))active @endif">
            <a href="{{ route('admin_store', ['store_expires_status' => '1']) }}">待续约包年店铺</a>
        </li>
    </ul>
</div>
@endif
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="name">店铺名称：</label>
            <input type="text" class="form-control" name="name" value="{{ $form['name'] or '' }}" placeholder="请输入姓名">
        </div>
        <div class="form-group">
            <label class="control-label" for="status">状态</label>
            <select name="status">
                <option value="" @if(!isset($form['status']) || $form['status'] == '') selected="selected" @endif>全部</option>
                <option value="0" @if(isset($form['status']) && $form['status'] == '0') selected="selected" @endif>未提交审核</option>
                 <option value="1" @if(isset($form['status']) && $form['status'] == '1') selected="selected" @endif>审核中</option>
                <option value="2" @if(isset($form['status']) && $form['status'] == '2') selected="selected" @endif>已批准</option>
                <option value="-1" @if(isset($form['status']) && $form['status'] == '-1') selected="selected" @endif>已拒绝</option>
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
        <input type="hidden" name="pa" value="{{ $form['pa'] or '' }}">
        <input type="hidden" name="page" value="{{ $form['store_expires_status'] or '' }}">
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $stores->total() }} 个申请 {{ $stores->firstItem() }}-{{ $stores->lastItem() }}
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
                <th>会员姓名/昵称</th>
                <th>会员等级</th>
                <th>注册时间</th>
                <th>网店等级</th>
                <th>店铺名称</th>
                <th>身份证号</th>
                <th>联系人</th>
                <th>联系电话</th>
                <th>主营主体</th>
                <th>预售商品描述</th>
                <th>地址</th>
                <th>文件</th>
                <th>申请时间</th>
                <th>审核状态</th>
                <th>到期时间</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($stores))
                    @foreach($stores as $key=>$list)
                        <tr>
                            <td>{{ !empty($list['userinfo']->id) ? $list['userinfo']->id : '' }}</td>
                            <td><a href="{{ route('admin_customer_info', $list['userinfo']['id']) }}" target="_blank">{{ !empty($list['userinfo']) &&  !empty($list['userinfo']['fullname']) ? $list['userinfo']['fullname'] : $list['userinfo']['nickname'] }}</a></td>
                            <td>{{ !empty($list['userinfo']) &&  !empty($level_status[$list['userinfo']->level_status]) ?  $level_status[$list['userinfo']->level_status] : '' }}</td>
                            <td>{{ $list['userinfo']->created_at or '' }}</td>
                            <td>{{ !empty($list['userinfo']) &&  isset($store_level_text[$list['userinfo']->store_level]) ? $store_level_text[$list['userinfo']->store_level] : '' }}</td>
                            <td><a href="{{ route('store_view', $list['id']) }}" target="_blank">{{ $list['name'] }}</a></td>
                            <td>{{ $list['id_card'] }}</td>
                            <td>{{ $list['contact_user_name'] }}</td>
                            <td>{{ $list['contact_phone'] }}</td>
                            <td>{{ $list['business_entity_name'] }}</td>
                            <td>{{ $list->description }}</td>
                            <td width="150">{{ $list->provice }} {{ $list->city }} {{ $list->district }} {{ $list->town }} {{ $list->village }} {{ $list->address }}</td>
                            <td>
                                <p>营业执照 <a href="{{ HelperImage::storagePath($list['business_license_front']) }}" target="_blank"><img src="{{ HelperImage::storagePath($list['business_license_front']) }}" width="20" height="20" /></a></p>
                                <p>身份证正面 <a href="{{ HelperImage::storagePath($list['id_card_front']) }}" target="_blank"><img src="{{ HelperImage::storagePath($list['id_card_front']) }}" width="20" height="20" /></a></p>
                                <p>身份证反面 <a href="{{ HelperImage::storagePath($list['id_card_back']) }}" target="_blank"><img src="{{ HelperImage::storagePath($list['id_card_back']) }}" width="20" height="20" /></a></p>
                            </td>
                            <td>{{ $list->created_at }}</td>
                            <td style="width: 120px">
                                @if($list['status'] == '1')
                                    <span style="color: #f00">
                                        {{ $store_status[$list['status']] }}
                                    </span>
                                @else
                                    <span>
                                        {{ $store_status[$list['status']] }}
                                    </span>
                                @endif

                                @if($list['status'] == '2')
                                <p style="color: #f00">{{ $list->approval_time }}</p>
                                @endif
                                @if($list['status'] == '-1' && $list->denial_time)
                                <p style="color: #f00">{{ $list->denial_time }}</p>
                                @endif
                            </td>
                            <td>{{ $list->expire_date }}</td>
                            <td>{{ $list->remarks }}</td>
                            <td>
                                @if($list['status'] == '1')
                                <a type="button" class="btn btn-primary handerApply"  href="javascript:void(0)" data-id="{{ $list['id'] }}">
                                    审批
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
<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="hander-apply-modal">
    <div class="modal-dialog" style="width: 1100px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    审批
                </h4>
            </div>
            <form class="form-horizontal hander-apply-form" role="form" action="" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" class="apply_id" value="">
                     <div class="form-group">
                        <label for="isdisabled" class="col-sm-2 control-label">批准</label>
                        <div class="col-sm-9">
                            <select class="form-control" value="" name="approval">
                                <option value="1">批准</option>
                                <option value="0">拒绝申请</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="image" class="col-sm-2 control-label">原因</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-info">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
           //显示添加
            $(".handerApply").on("click",function(){
                var id = $(this).attr('data-id');
                $("#hander-apply-modal").modal({backdrop: 'static'});
                var form = $(".hander-apply-form");
                form.find(".apply_id").val(id);
                form.find(".remarks").val('');
            }); 
            $(".hander-apply-form").on("submit", function(){
                var form = $(this);
                var data = form.serializeObject();
                $.showLoad(true, true);
                $.postAjax('/admin/store/handerApply', data, function(result) {
                    if(result.code == '200'){
                        window.location.reload();
                    } else {
                        $.hideLoad();
                        $.showMessage(result.message);
                    }
                });
            });
        };
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection
