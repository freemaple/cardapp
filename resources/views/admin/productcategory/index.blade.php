@extends('admin.template.layout')
@section('styles')
{!! App\Assets\Admin::style('admin/jstree/jstree.css') !!}
@endsection
@section('content')
<ul class="breadcrumb">
    <li><a href="/admin/productcategory">产品管理</a></li>
    <li class="active">产品分类</li>
</ul>
<div class="tree_panel clearfix">
    <div class="tree_aside" style="display: none">
        <div id="productcategory_tree"></div>
    </div>
    <div class="tree_content">
        <div class="panel panel-info">
            <div class="panel-body">
                <div class="well well-sm">
                    <span>
                        <a href="javascript:void(0)" class="btn btn-info add_productcategory">添加分类</a>
                    </span>
                </div>
                <div class="well">
                    <form class="form-inline" role="form">
                        <div class="form-group">
                            <label class="control-label" for="name">分类名称</label>
                            <input type="text" class="form-control" name="name" value="{{ isset($form['name']) ? $form['name'] : ''  }}" placeholder="请输入名称">
                        </div>
                        <input type="hidden" name="pid" value="{{ isset($form['pid']) ? $form['pid'] : '' }}">
                        <button type="submit" class="btn btn-info">查询</button>
                    </form>
                </div>
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>编号</th>
                            <th>名称</th>
                            <th>描述</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody class="productcategory_table_list">
                       @include('admin.productcategory.block.productcategory_list', ['productcategory_list' => $productcategory_list])
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="save_productcategory_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:760px;margin-top:20px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    产品分类信息
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal productcategory_form ajax_form" role="form"  name="productcategory_form" action="/admin/productcategory/save" onsubmit="return false">
                    {!! csrf_field() !!}
                    <input name="save_type" class="save_type" type="hidden" value="0">
                    <input name="id" class="form-control" type="hidden" />
                    <div class="form-group productcategory_pid_line">
                        <label class="col-sm-2 control-label" for="nickname">父级分类</label>
                        <div class="col-sm-10">
                            <select name="pid" class="form-control">
                                <option value="">请选择</option>
                                @if(!empty($productcategory_select_list))
                                @foreach ($productcategory_select_list as $key => $category)
                                <option value="{{ $category['id'] }}">@if($category['level'] == 0)&nbsp;@else @for($i = 0; $i <= $category['level']; $i++) &nbsp; @endfor @endif {{$category['name'] }}</option>  
                                @endforeach ?>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">名称</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="input" name="name" required="required" maxlength="255" placeholder="请输入名称" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">描述</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" type="input" name="description" required="required" maxlength="255" placeholder="请输入描述" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">启用</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="is_enable">
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
{!! App\Assets\Admin::script('admin/scripts/plugin/jstree.min.js', '') !!}
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
            var data = <?= json_encode($productcategory_tree) ?>;
            $('#productcategory_tree').jstree({
                plugins : ["types","themes"], 
                "types": {
                    "default" : {
                        "icon" : 'glyphicon glyphicon-ok'  // 关闭默认图标
                    }
                  },
                'core' : {
                    "multiple": false,
                   'data': data
                }
            });
            $('#productcategory_tree').on("changed.jstree", function (e, data) {
                self.selectnode = data.node;
                if(data.node){
                    var id = data.node.id;
                    $.showLoad();
                    var search_url = window.location.search;
                    if(id != null){
                        search_url = $.changeURLArg(search_url, 'pid', id);
                    } else {
                        search_url = $.delParam(search_url, 'pid');
                    }
                    search_url = $.delParam(search_url, 'name');
                    window.location.href = search_url + window.location.hash;
                }
            });
            $(".productcategory_form").validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 64
                    },
                    description: {
                        required: true,
                        maxlength: 50
                    },
                    status: {
                        required: true
                    }
                }
            });
            //显示添加
            $(".add_productcategory").on("click",function(){
                $("#save_productcategory_modal").modal();
                var form = $(".productcategory_form");
                $.clearForm(form);
                form.find('.save_type').val('0');
                form.find('[name="pid"]').val('');
            }); 
            $('#productcategory_select').jstree({
                'core' : {
                    "multiple": false,
                   'data': data
                }
            });
            this.listEvent();
        };
        app.listEvent = function(){
            //编辑
            $(".update_productcategory").on("click", function(){
                var elem = $(this);
                $.showLoad();
                var id = elem.attr("data-id");
                var action = elem.attr('data-action');
                $.postAjax(action, {'id': id}, function(result) {
                    $.hideLoad();
                    if(result.data){
                        var form = $(".productcategory_form");
                        $.loadForm(form, result.data);
                        $("#save_productcategory_modal").modal();
                        form.find('.save_type').val('1');
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