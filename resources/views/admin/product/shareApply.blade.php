@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_customer') }}">店铺管理</a></li>
    <li class="active">共享专区申请记录</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
       
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $shareApplys->total() }} 个申请记录 {{ $shareApplys->firstItem() }}-{{ $shareApplys->lastItem() }}
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
                <th>头像</th>
                <th>店铺</th>
                <th>产品</th>
                <th>审核状态</th>
                <th>提交时间</th>
                <th>审核时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($shareApplys))
                    @foreach($shareApplys as $key=>$list)
                        <tr>
                            <td>
                                {{ $list->user_id }}
                                <a type="button" class="text-info"  href="/admin/customer/{{ $list['user_id'] }}" target="_blank">
                                    详情
                                </a>
                            </td>
                             <td>{{ $list->user->phone ? $list->user->phone : $list->phone }}</td>
                            <td>{{ $list->user->fullname ? $list->user->fullname : $list->nickname }}</td>
                            <td><a href="{{ HelperImage::getavatar($list->user->avatar) }}" target="_blank"><img src="{{ HelperImage::getavatar($list->user->avatar) }}" width="40"></a></td>
                            </td>
                            <td>@if(!empty($list->store)) <a href="{{ route('store_view', [$list->store['id']]) }}" target="_blank">{{ $list->store['name'] }}</a> @endif</td>
                            <td>
                                {{ $list['product']['name'] }}<br />
                                <a target="_blank" href="{{ $list['product']['main_image'] }}"><img src="{{ $list['product']['main_image'] }}" width="40" height="40"></a>
                            </td>
                            <td>{{ $status_text[$list->status] }}</td>
                            <td>{{ $list->created_at }}</td>
                            <td>{{ $list->approval_time }}</td>
                            <td>
                                @if($list['status'] == 0)
                                <a href="javascript:void(0)" data-id="{{ $list['id'] }}" class="approvalApply" data-type='1' data-confirm="通过">通过</a>
                                <a href="javascript:void(0)" data-id="{{ $list['id'] }}" class="approvalApply" data-type='0' data-confirm="拒绝">拒绝</a>
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
                    <input type="hidden" name="type" class="apply_type" value="">
                    <div class="form-group">
                        <label for="remarks" class="col-sm-2 control-label">原因</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="remarks" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-info">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(".approvalApply").on('click', function(){
        var type = $(this).attr('data-type');
        var confirm = $(this).attr('data-confirm');
        var id = $(this).attr('data-id');
        if(type == 0){
            
        } 
        $(".apply_id").val(id);
        $(".apply_type").val(type);
        $("#hander-apply-modal").modal();
    });
    $('.hander-apply-form').on("submit", function(){
        var form = $(this);
        let data = form.serializeObject();
        if(data.type == '0'){
            if(data.remarks == ''){
                $.showMessage('请输入原因');
                return false;
            }
        }
        $.post('/admin/product/shareApplyApproval', data, function(rst){
            if(rst.code == 'Success'){
                window.location.reload();
            } else {
                $.showMessage(rst.message);
            }
        }, 'json');
    });
</script>
@endsection
