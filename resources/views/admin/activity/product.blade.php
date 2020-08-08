@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
    <li><a href="/admin/productcategory">活动分类</a></li>
    <li class="active">{{ $activity_category['description'] }}-产品</li>
</ul>
<div class="well well-sm">
    <span>
        <a href="javascript:void(0)" class="btn btn-info add_product">添加产品</a>
    </span>
</div>
<div class="tree_panel clearfix">
   <div class="panel panel-info">
        <div class="panel-body">
            <div class="well">
                <form class="form-inline" role="form">
                    <div class="form-group">
                        <label class="control-label" for="name">产品名称</label>
                        <input type="text" class="form-control" name="name" value="{{ isset($form['name']) ? $form['name'] : ''  }}" placeholder="请输入名称">
                    </div>
                    <input type="hidden" name="pid" value="{{ isset($form['pid']) ? $form['pid'] : '' }}">
                    <button type="submit" class="btn btn-info">查询</button>
                </form>
            </div>
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th>产品id</th>
                        <th>产品名称</th>
                        <th>产品图片</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody class="productcategory_table_list">
                   @foreach($products as $key => $list)
                        <tr class="level">
                            <td>{{ $list['product_id'] }}</td>
                            <td><a target="_blank" href="/product/{{ $list['product_id'] }}">{{ $list['product_name'] }}</a></td>
                            <td><a target="_blank" href="/product/{{ $list['product_id'] }}"><img src="{{ $list['image'] }}" width="80" /></a></td>
                            <td>{{ $list['created_at'] }}</td>
                            <td>
                                <a type="button" class="btn btn-info remove_product"  data-action="/admin/activitycategory/removeProduct" data-id="{{ $activity_category['id'] }}" data-product-id="{{ $list['product_id'] }}">
                                    删除
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="add_product_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width:760px;margin-top:20px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加产品
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal ajax_form add_product_form" role="form"  name="add_product_form" action="/admin/activitycategory/addProduct" onsubmit="return false">
                    <input type="hidden" name="activity_category_id" value="{{ $activity_category['id'] }}" />
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="location" class="col-sm-2 control-label">产品id</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="product_ids" required="required" maxlength="255" placeholder="产品id"></textarea>
                            <span>多个用;分割</span>
                        </div>
                    </div>
                    </div>
                    <div class="text-center" style="padding: 20px 0px;">
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
            //显示添加
            $(".add_product").on("click",function(){
                $("#add_product_modal").modal();
                var form = $(".add_product_form");
                form.find('[name="product_ids"]').val('');
            }); 
            this.listEvent();
        };
        app.listEvent = function(){
            //编辑
            $(".remove_product").on("click", function(){
                var elem = $(this);
                $.showConfirm('确定删除？', function(){
                    $.showLoad();
                    var id = elem.attr("data-id");
                    var action = elem.attr('data-action');
                    var product_id = elem.attr('data-product-id');
                    $.postAjax(action, {'id': id, 'product_id': product_id}, function(result) {
                        $.hideLoad();
                        if(result.code == '200'){
                           window.location.reload();
                        } else {
                            $.showMessage(result.message);
                        }
                    });
                })
            });
        }
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection