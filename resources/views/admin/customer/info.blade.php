
@extends('admin.template.layout')
@section('content')
<div class="content">
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">基础信息</h3>
        </div>
        <div class="panel-body">
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th>会员id</th>
                        <th>手机号码</th>
                        <th>姓名/昵称</th>
                         <th>头像</th>
                        <th>微信号</th>
                        <th>微信二维码</th>
                        <th>性别</th>
                        <th>注册ip地址</th>
                        <th>登录次数</th>
                        <th>最近一次登录</th>
                        <th>注册时间</th>
                        <th>状态</th>
                    </tr>
                </thead>
               <tbody>
                  <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->fullname ? $user->fullname : $user->nickname }}</td>
                        <td><a href="{{ HelperImage::getavatar($user->avatar) }}" target="_blank"><img src="{{ HelperImage::getavatar($user->avatar) }}" width="40"></a></td>
                        <td>{{ $user->weixin }}</td>
                        <td><a href="{{ HelperImage::storagePath($user->weixin_qr) }}" target="_blank"><img src="{{ HelperImage::storagePath($user->weixin_qr) }}" width="40"></a></td>
                        <td>{{ $user->gender }} </td>
                        <td>{{ $user->signup_ip  }}</td>

                        <td>{{ $user->login_times }}</td>
                        <td>{{ date("Y-m-d H:i:s ", $user->lastlogin) }}</td>
                        <td>{{ $user->created_at }}</td>
                        <td>{{ $user->enable == "1" ? "正常" : "黑名单" }}</td>
                    </tr>
               </tbody>
            </table>

            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th>会员等级</th>
                        <th>会员类型</th>
                        <th>vip到期时间</th>
                        <th>店铺</th>
                        <th>店铺等级</th>
                        <th>荣誉积分</th>
                        <th>推荐人</th>
                        <th>直推人数</th>
                        <th>剩余积分</th>
                        <th>店铺营业积分</th>
                        <th>剩余赏金</th>
                        <th>冻结中赏金</th>
                        <th>剩余代购积分</th>
                    </tr>
                </thead>
               <tbody>
                  <tr>
                        <td>{{ $level_status[$user->level_status] or '' }} </td>
                        <td style="width: 120px;">
                            <span>{{ $user_type[$user->user_type]  }}</span>
                            @if(!$user['user_type'] || $user['user_type'] == 'general')
                            @if(array_intersect(['admin'], $admin_user['allRoles']))
                                <p style="margin: 5px 0px;">
                                    <a href="javascript:void(0)" class="setUserType text-info" data-id="{{ $user['id'] }}" data-usertype="manager" data-confirm="确认设置{{ $user['fullname'] }}为总监？" >修改为总监</a><br />
                                    <a href="javascript:void(0)" class="setUserType text-danger" data-id="{{ $user['id'] }}" data-usertype="director" data-confirm="确认设置{{ $user['fullname'] }}为总经理？">修改为总经理</a>
                                </p>
                            @endif
                            @endif
                        </td>
                        <td>{{ $user->vip_end_date  }}</td>
                        <td>@if(!empty($user->store)) <a href="{{ route('store_view', [$user->store['id']]) }}" target="_blank">{{ $user->store['name'] }}</a> @endif</td>
                        <td>{{ !empty($user->store) ? $store_level_list[$user->store_level] : '' }} </td>
                        <td>{{ $user->honor_value  }} : {{ $user->honor_vip_value  }}</td>
                        <td>{{ $user->referrer_user['fullname'] or '' }}</td>
                        <td>{{ $user->referrer_user_count  }}</td>
                        <td><a href="{{ route('admin_customer_integral', $user->id) }}" target="_blank">￥{{ !empty($user->integral_info) ?  $user->integral_info['point'] : 0 }}</a></td>
                        <td>￥{{ !empty($user->store_sales_points) ?  $user->integral_info['store_sales_points'] : 0 }}</td>
                        <td><a href="{{ route('admin_customer_reward', $user->id) }}" target="_blank">￥{{ !empty($user->reward_info) ?  $user->reward_info['amount'] : 0 }}</a></td>
                        <td>￥{{ !empty($user->reward_info) ?  $user->reward_info['freeze_amount'] : 0 }}</td>
                        <td>￥{{ !empty($user->sub_integral_amount) ?  $user->sub_integral_amount : 0 }}</td>
                    </tr>
               </tbody>
            </table>
        </div>
    </div>
    @if(!empty($user['store']))
    <div class="panel panel-info">
        <div class="panel-heading">
          <h3 class="panel-title">店铺信息</h3>
        </div>
        <div class="panel-body">
            <table class="table table-condensed table-striped">
               <thead>
                  <tr>
                    <th>网店等级</th>
                    <th>店铺名称</th>
                    <th>联系人</th>
                    <th>联系电话</th>
                    <th>主营主体</th>
                    <th>预售商品描述</th>
                    <th>地址</th>
                    <th>文件</th>
                    <th>申请时间</th>
                    <th>审核状态</th>
                    <th>备注</th>
                  </tr>
               </thead>
               <tbody>
                    <tr>
                        <td>{{ isset($store_level_text[$user->store_level]) ? $store_level_text[$user->store_level] : '' }}</td>
                        <td>{{ $user['store']['name'] }}</td>
                        <td>{{ $user['store']['contact_user_name'] }}</td>
                        <td>{{ $user['store']['contact_phone'] }}</td>
                        <td>{{ $user['store']['business_entity_name'] }}</td>
                        <td>{{ $user['store']->description }}</td>
                        <td width="150">{{ $user['store']->provice }} {{ $user['store']->city }} {{ $user['store']->district }} {{ $user['store']->town }} {{ $user['store']->village }} {{ $user['store']->address }}</td>
                        <td>
                            <p>营业执照 <a href="{{ HelperImage::storagePath($user['store']['business_license_front']) }}" target="_blank"><img src="{{ HelperImage::storagePath($user['store']['business_license_front']) }}" width="20" height="20" /></a></p>
                            <p>身份证正面 <a href="{{ HelperImage::storagePath($user['store']['id_card_front']) }}" target="_blank"><img src="{{ HelperImage::storagePath($user['store']['id_card_front']) }}" width="20" height="20" /></a></p>
                            <p>身份证反面 <a href="{{ HelperImage::storagePath($user['store']['id_card_back']) }}" target="_blank"><img src="{{ HelperImage::storagePath($user['store']['id_card_back']) }}" width="20" height="20" /></a></p>
                        </td>
                        <td>{{ $user['store']->created_at }}</td>
                        <td style="width: 120px">
                            @if($user['store']['status'] == '1')
                                <span style="color: #f00">
                                    {{ $store_status[$user['store']['status']] }}
                                </span>
                            @else
                                <span>
                                    {{ $store_status[$user['store']['status']] }}
                                </span>
                            @endif

                            @if($user['store']['status'] == '2')
                            <p style="color: #f00">{{ $user['store']->approval_time }}</p>
                            @endif
                            @if($user['store']['status'] == '-1' && $user['store']->denial_time)
                            <p style="color: #f00">{{ $user['store']->denial_time }}</p>
                            @endif
                        </td>
                        <td>{{ $user['store']->remarks }}</td>
                        <td>
                            @if($user['store']['status'] == '1')
                            <a type="button" class="btn btn-primary handerApply"  href="javascript:void(0)" data-id="{{ $user['store']['id'] }}">
                                审批
                            </a>
                            @endif
                        </td>
                    </tr>
               </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(".setUserType").on("click", function(){
        var id = $(this).attr('data-id');
        var user_type = $(this).attr('data-usertype');
        var confirm = $(this).attr('data-confirm');
        $.showConfirm(confirm, function(){
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