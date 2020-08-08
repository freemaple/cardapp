@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="/admin/payout/apply">提现管理</a></li>
    <li class="active">提现申请</li>
</ul>
<div class="panel panel-info">
    <div class="panel-body">
        <h3 class="panel-title">总体提现统计</h3>
        <table class="table table-condensed table-striped">
            <thead>
                <tr>

                    <th>总笔数</th>
                    <th>总申请金额</th>
                    <th>总应打款金额</th>

                    <th>待处理笔数</th>
                    <th>待处理提现金额</th>
                    <th>待处理应打款金额</th>

                    <th>总已处理笔数</th>
                    <th>总已处理金额</th>
                    <th>总已处理应打款金额</th>

                </tr>
            </thead>
           <tbody>
                <tr>
                    <td>{{ $statistics['payout_count'] }}</td>
                    <td>{{ $statistics['payout_amount'] }}</td>
                    <td>{{ $statistics['payout_actual_amount'] }}</td>

                    <td>
                        <span style="color: #f00;font-size: 28px">{{ $statistics['processing_count'] }}</span>
                    </td>

                    <td>
                        <span style="color: #f00;font-size: 28px">{{ $statistics['processing_amount'] }}</span>
                    </td>
                    <td>
                        <span style="color: #f00;font-size: 28px">{{ $statistics['processing_actual_amount'] }}</span>
                    </td>

                    <td>{{ $statistics['processed_count'] }}</td>
                    <td>{{ $statistics['processed_amount'] }}</td>
                    <td>{{ $statistics['processed_actual_amount'] }}</td>
                </tr>
           </tbody>
        </table>
    </div>
</div>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">真实姓名：</label>
            <input type="text" class="form-control" name="fullname" value="{{ $form['fullname'] or '' }}" placeholder="请输入姓名">
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">状态</label>
            <select name="status">
                <option value="">全部</option>
                <option value="1" @if(isset($form['status']) && $form['status'] == '1') selected="selected" @endif>待处理</option>
                <option value="2" @if(isset($form['status']) && $form['status'] == '2') selected="selected" @endif>已拨款</option>
                <option value="0" @if(isset($form['status']) && $form['status'] == '0') selected="selected" @endif>已拒绝</option>
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
        共 {{ $payoutApplys->total() }} 个申请 {{ $payoutApplys->firstItem() }}-{{ $payoutApplys->lastItem() }}
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
                <th>手机号码</th>
                <th>姓名/昵称</th>
                <th>会员等级</th>
                <th>注册时间</th>
                <th>提现编号</th>
                <th>提现金额</th>
                <th>提现打款金额</th>
                <th>真实姓名</th>
                <th>支付宝帐号</th>
                <th>审核状态</th>
                <th>申请时间</th>
                <th>备注</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($payoutApplys))
                    @foreach($payoutApplys as $key=>$list)
                        <tr>
                            <td>{{ $list['userinfo']->id or '' }}</td>
                            <td>{{ $list['userinfo']->phone or '' }}</td>
                            <td><a href="{{ route('admin_customer_info', $list['userinfo']['id']) }}" target="_blank">{{ $list['userinfo']->fullname ? $list['userinfo']->fullname : $list['userinfo']->nickname }}</a></td>
                            <td>{{ $level_status[$list['userinfo']->level_status] or '' }}</td>
                            <td>{{ $list['userinfo']->created_at or '' }}</td>
                            <td>{{ $list->number }}</td>
                            <td>{{ $list->amount }}</td>
                            <td style="color: #f00">{{ $list->actual_amount }}</td>
                            <td>{{ $list->fullname }}</td>
                            <td>{{ $list->alipay }}</td>
                            <td>
                                {{ $payout_status[$list['status']] }}
                                @if(!empty($list['approve_time']))
                                    {{ $list['approve_time'] }}
                                @endif
                            </td>
                            <td>{{ $list->created_at }}</td>
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
    <div class="modal-dialog" style="width: 1100px;max-width: 98%">
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
                            <option value="1">已打款</option>
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
                $.postAjax('/admin/payout/handerApply', data, function(result) {
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
