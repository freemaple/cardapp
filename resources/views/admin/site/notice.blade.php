@extends('admin.template.layout')
@section('content')
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
             <label class="control-label" for="location">位置</label>
             <select name="location" class="form-control">
                <option value="">所有</option>
                @foreach($locations as $lo => $l_text)
                <option value="{{ $lo }}" @if(isset($form['location']) && $form['location'] == $lo) selected="selected" @endif>{{ $l_text }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
             <label class="control-label" for="location">是否启用</label>
             <select name="enabled" class="form-control">
                <option value="" @if(!isset($form['enabled']) || $form['enabled'] === '') selected="selected" @endif>所有</option>
                <option value="1" @if(isset($form['enabled']) && $form['enabled'] == '1') selected="selected" @endif>启用</option>
                <option value="0" @if(isset($form['enabled']) && $form['enabled'] === '0') selected="selected" @endif>禁用</option>
            </select>
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
<div class="well well-sm">
    <span>
        <a href="javascript:void(0)" class="btn btn-info add_notice">添加通知</a>
    </span>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
       共 {{ $notices->total() }} 个通知   当前页：{{ $notices->firstItem() }}-{{ $notices->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th>位置</th>
                <th>内容</th>
                <th>是否使用</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($notices))
                    @foreach($notices as $key=>$list)
                        <tr>
                            <td>{{ $locations[$list['location']] or '' }}</td>
                            <td>{{ $list['content'] }}</td>
                            <td>{{ $list['enabled'] == 1 ? '启用' : '禁用' }}</td>
                            <td>
                                <a type="button" class="btn btn-info update_notice" data-id="<?= $list['id'] ?>" data-action="/admin/notice/load" href="javascript:void(0)">
                                    修改
                                </a>
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
<div class="modal fade" id="save_notice_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:760px;margin-top:20px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加通知
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal notice_form ajax_form" role="form"  name="productcategory_form" action="/admin/notice/save" onsubmit="return false">
                    <input type="hidden" name="id" />
                    <input type="hidden" name="save_type" class="save_type" />
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="location" class="col-sm-2 control-label">位置</label>
                        <div class="col-sm-10">
                            <select name="location" class="form-control" required="required">
                                <option value="">请选择</option>
                                @foreach($locations as $lo => $l_text)
                                <option value="{{ $lo }}">{{ $l_text }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="content" class="col-sm-2 control-label">内容</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" type="input" name="content" required="required"  placeholder="请输入描述" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">启用</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="enabled">
                                <option value="">请选择</option>
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-center">
                        <input type="submit" class="btn btn-large btn-info" value="保存" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
            $(".notice_form").validate({
                rules: {
                    location: {
                        required: true,
                        maxlength: 64
                    },
                    content: {
                        required: true
                    },
                    enabled: {
                        required: true
                    }
                }
            });
            //显示添加
            $(".add_notice").on("click",function(){
                $("#save_notice_modal").modal();
                var form = $(".notice_form");
                $.clearForm(form);
                form.find('.save_type').val('0');
                form.find('[name="location"]').removeAttr('disabled').val('');
                form.find('[name="id"]').val('');
            }); 
            this.listEvent();
        };
        app.listEvent = function(){
            //编辑
            $(".update_notice").on("click", function(){
                var elem = $(this);
                $.showLoad();
                var id = elem.attr("data-id");
                var action = elem.attr('data-action');
                $.postAjax(action, {'id': id}, function(result) {
                    $.hideLoad();
                    if(result.data){
                        var form = $(".notice_form");
                        $.loadForm(form, result.data);
                        $("#save_notice_modal").modal();
                        form.find('.save_type').val('1');
                        form.find('[name="id"]').val(id);
                        form.find('[name="location"]').prop('disabled', 'disabled').find('option[value="' + result.data.location + '"]').prop('selected', 'selected');
                        form.find('[name="enabled"]').find('option[value="' + result.data.enabled + '"]').prop('selected', 'selected');
                    } else {
                        $.showMessage(result.message);
                    }
                });
            });
        }
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection
