@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">会员管理</a></li>
    <li class="active">会员信息</li>
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
            <label class="control-label" for="nickname">用户类型</label>
            <select name="level_status">
                <option value="" @if((!isset($form['level_status']) || $form['level_status'] == '')) selected="selected" @endif>全部</option>
                @foreach($level_status as $level_status_id => $l_text)
                <option value="{{ $level_status_id }}" @if(isset($form['level_status']) && $form['level_status'] == $level_status_id &&  $form['level_status'] !== '')selected="selected" @endif>{{ $l_text }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">店铺类型</label>
            <select name="store_level">
                <option value="" @if((!isset($form['store_level']) || $form['store_level'] == '')) selected="selected" @endif>全部</option>
                @foreach($store_level_list as $store_level_value => $store_level_text)
                <option value="{{ $store_level_value }}" @if(isset($form['store_level']) && $form['store_level'] == $store_level_value &&  $form['store_level'] !== '')selected="selected" @endif>{{ $store_level_text }}</option>
                @endforeach
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
<div class="clearfix"><div style="color: #f00;font-size: 20px;padding: 0px 30px 0px 20px">共送出积分 ￥{{ $integral_send_total }}</div></div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $userlist->total() }} 个用户 {{ $userlist->firstItem() }}-{{ $userlist->lastItem() }}
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
                <th>会员类型</th>
                <th>手机号码</th>
                <th>登录用户名</th>
                <th>姓名/昵称</th>
                <th>头像</th>
                <th>会员等级</th>
                <th>vip到期时间</th>
                <th>店铺</th>
                <th>店铺等级</th>
                <th>店铺到期时间</th>
                <th>登录次数</th>
                <th>最近一次登录</th>
                <th>注册时间</th>
                <th>推荐人</th>
                <th>直推人数</th>
                <th>剩余积分</th>
                <th>剩余代购积分</th>
                <th>剩余麦粒</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($userlist))
                    @foreach($userlist as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->id }}
                                <a type="button" class="text-info"  href="/admin/customer/{{ $list['id'] }}" target="_blank">
                                    详情
                                </a>
                            </td>
                            <td style="width: 120px;">
                                <span>{{ $user_type[$list->user_type]  }}</span>
                            </td>
                            <td>
                                {{ $list->phone }}
                                <p>
                                    <a type="button" class="update_item"  href="{{ route('admin_post', ['user_id' => $list['id']]) }}" target="_blank">
                                        文章
                                    </a>
                                    <a type="button" class=""  href="{{ route('admin_card', ['user_id' => $list['id']]) }}" target="_blank">
                                        名片
                                    </a>
                                </p>
                            </td>
                            <td>{{ $list->user_name }}</td>
                            <td>{{ $list->fullname ? $list->fullname : $list->nickname }}</td>
                            <td><a href="{{ HelperImage::getavatar($list->avatar) }}" target="_blank"><img src="{{ HelperImage::getavatar($list->avatar) }}" width="40"></a></td>
                            </td>
                            <td>{{ $level_status[$list->level_status] or '' }} </td>
                            <td>{{ $list->vip_end_date  }}</td>
                            <td>@if(!empty($list->store)) <a href="{{ route('store_view', [$list->store['id']]) }}" target="_blank">{{ $list->store['name'] }}</a> @endif</td>
                            <td>{{ !empty($list->store) ? $store_level_list[$list->store_level] : '' }} </td>
                            <td>{{ !empty($list->store) ? $list->store['expire_date'] : '' }} </td>
                            <td>{{ $list->login_times }}</td>
                            <td style="width: 80px">{{ date("Y-m-d H:i:s ", $list->lastlogin) }}</td>
                            <td style="width: 80px">{{ $list->created_at }}</td>
                            <td>{{ $list->referrer_user['fullname'] or '' }}</td>
                            <td>{{ $list->referrer_user_count  }}</td>
                            <td style="color: #f00">
                                ￥{{ $list['integral_amount'] }}
                                @if($list['integral_send_total'] > 0)
                                    <div><span style="color: #00f;font-size: 16px">已送 {{ $list['integral_send_total'] }}</span></div>
                                @endif
                            </td>
                            <td>{{ $list->sub_integral_amount }}</td>
                            <td>{{ $list->gift_commission }}</td>
                            <td>{{ $list->enable == "1" ? "正常" : "黑名单" }}</td>
                            <td>
                                @if(array_intersect(['admin'], $admin_user['allRoles']))
                                    <a type="button" href="javascript:void(0)" style="color: #f00" class="showIntegralSendModal" data-id={{ $list['id'] }} data-fullname="{{ $list->fullname }}">
                                    赠送积分
                                    </a>
                                    @if($list['is_vip'] != '1')
                                    <a href="javascript:void(0)" class="user_open" data-href="/admin/customer/openvip?user_id={{ $list['id'] }}">开通金麦</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>  
<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="integralSendModal">
    <div class="modal-dialog" style="width: 600px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    赠送积分
                </h4>
            </div>
            <form class="form-horizontal integralSendForm" role="form" action="/admin/customer/integralSend" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="user_id" class="user_id" value="">
                    <input type="hidden" class="fullname" value="" disabled="disabled">
                    <div class="well well-sm">
                        <span>当前用户剩余积分: <span class="integral_amount" style="color: #f00;font-size: 16px"></span></span>
                        <span>当前已赠送次数: <span class="send_integral_count" style="color: #f00;font-size: 16px"></span></span>
                        <span>当前已赠送积分: <span class="send_integral_sum" style="color: #f00;font-size: 16px"></span></span>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">积分</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="number" step="0.00" name="integral" required="required"  />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">备注</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" rows="2" name="content"  maxlength="255" required="required"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-info">发送</button>
                </div>
            </form>
        </div>
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
    $(".user_open").on('click', function(){
        var href = $(this).attr('data-href');
        $.showConfirm("确认开通？", function(){
            window.location.href = href;
        });
    });
    $(".showIntegralSendModal").on('click', function(){
        var id = $(this).attr('data-id');
        var fullname = $(this).attr('data-fullname');
        var modal = $("#integralSendModal");
        modal.find('.user_id').val(id);
        modal.find('.fullname').val(fullname);
        $.post('/admin/customer/loadUserIntegral', {'user_id' : id}, function(rst){
            if(rst.code == '200'){
                modal.find('.send_integral_count').text(rst['data']['send_integral_count']);
                modal.find('.send_integral_sum').text("￥" + rst['data']['send_integral_sum']);
                var integral_amount = rst['data']['integral'] ? rst['data']['integral']['point'] : 0;
                modal.find('.integral_amount').text("￥" + integral_amount);
                modal.modal();
            } else {
                $.showMessage(rst.message)
            }
        }, 'json');
    });
    $(".integralSendForm").on("submit", function(){
        var form = $(this);
        var fullname = form.find('.fullname').val();
        var modal = $("#integralSendModal");
        var send_integral_count = form.find('.send_integral_count').text();
        if(send_integral_count > 0){
            var confirm = '已经给' + fullname + '赠送过积分，还继续赠送？';
        } else {
            var confirm = '确认给' + fullname + '赠送积分?';
        }
        $.showConfirm(confirm, function(){
            var data = form.serializeObject();
            $.showLoad(true);
            modal.modal('hide');
            $.post('/admin/customer/integralSend', data, function(rst){
                if(rst.code == '200'){
                    $.hideLoad();
                    $.showMessage(rst.message, function(){
                        window.location.reload();
                    });
                } else {
                    modal.modal('show');
                    $.hideLoad();
                    $.showMessage(rst.message)
                }
            }, 'json');
        });
    });
    $(".setUserType").on("click", function(){
        var id = $(this).attr('data-id');
        var user_type = $(this).attr('data-usertype');
        $.showConfirm('设置为总经理', function(){
            $.post('/admin/customer/setUserType', {'user_id' : id, 'user_type': user_type}, function(rst){
                if(rst.code == '200'){
                    $.showMessage(rst.message, function(){
                        window.location.reload();
                    })
                } else {
                    $.showMessage(rst.message);
                }
            }, 'json'); 
        })
        
    });
</script>
@endsection
