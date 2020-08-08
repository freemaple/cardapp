@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
    <li>产品管理</li>
    <li class="active">{{ $product['spu'] }}({{ $product['name'] }})</li>
    <li>规格列表</li>
</ul>
<div class="well well-sm">
    @if($product['is_self'] == '1')
    <ul class="nav nav-pills">
        <a href="javascript:void(0)" class="btn btn-info j_load_sku_add"><span class="glyphicon glyphicon-plus"></span>添加规格</a>
    </ul>
    @endif
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" class="rows_check" /> </th>
                <th>编号</th>
                <th>图片</th>
                <th>库存</th>
                <th>价格($)</th>
                <th>原价($)</th>
                <th>共享积分($)</th>
                <th>运费($)</th>
                <th>是否上架</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($product_sku_list['data']))
                @foreach($product_sku_list['data'] as $key => $list)
                    <tr>
                        <td><input type="checkbox" class="row_check" data-id="{{ $list['id'] }}" /></td>
                        <td><?php echo $key+1 ?></td>
                        <td style="width: 50px"><img src="{{ HelperImage::storagePath($list['image']) }}" width="50"  />
                            @if($product['is_self'] == '1')
                            <p style="text-align: center;padding: 10px 0px">
                            <a href="javascript:void())" class="j_select_product_sku_image" data-modal-id="select_product_sku_image_modal" data-id="{{ $list['id'] }}">选择</a></p>
                            @endif
                        </td>
                        <td>{{ $list['stock'] }}</td>
                        <td>{{ $list['price'] }}</td>
                        <td>{{ $list['market_price'] }}</td>
                        <td>{{ $list['share_integral'] }}</td>
                        <td>{{ $list['shipping'] }}</td>
                        <td>{{ $list['is_sale'] == "1" ? "是" :"否" }}</td>
                        <td>{{ $list['created_at'] }}</td>
                        <td>
                            @if($product['is_self'] == '1')
                            <a class="btn btn-info j_load_sku_edit" data-id="{{ $list['id'] }}" data-action="/admin/api/product/sku/edit/load" href="javascript:void(0)">
                                修改
                            </a>
                            @endif
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
<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="select_product_sku_image_modal">
    <div class="modal-dialog" style="width: 1100px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    选择图片
                </h4>
            </div>
            <form class="form-horizontal save_product_sku_image" role="form" action="/admin/api/product/sku/saveimage" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="">
                    <div class="img-ctr-body clear-fix">
                        <ul class="image_list product-image-list">
                            @foreach($product_image as $ikey => $image)
                            @if($image['type'] == 'main')
                            <li style="display: inline-block;vertical-align: middle;margin-right: 10px;margin-bottom: 10px" data-name="{{ $image['image'] }}">
                                <img src="{{ $image['imgsrc'] }}" width="100" style="cursor: default;">
                                <p style="text-align: center;padding: 10px 0px"><input type="radio" name="image_check" value="{{ $image['image'] }}" /></p>
                            </li>
                            @endif
                            @endforeach
                        </ul>
                    </div>
                    <a href="javascript:void(0)" class="btn btn-info js-add-product-image">添加图片</a>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-info">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="edit_sku_box" style="display: none"></div>
<div class="modal fade"  tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true" id="add_product_sku_modal">
    <div class="modal-dialog" style="width: 900px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加sku
                </h4>
            </div>
            <form class="form-horizontal add-product-sku-form" role="form" action="/admin/api/product/sku/add" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                    @if(in_array('color', $attribute_option))
                    <div class="form-group">
                        <label for="color" class="col-sm-2 control-label">颜色</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="color" value="" placeholder="颜色">
                        </div>
                    </div>
                    @endif
                    @if(in_array('size', $attribute_option))
                    <div class="form-group">
                        <label for="size" class="col-sm-2 control-label">规格</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="size" value="" placeholder="规格">
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="price" class="col-sm-2 control-label">价格($)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" required="required" name="price" value="" placeholder="请输入价格($)">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="price" class="col-sm-2 control-label">市场价(元）</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="market_price" value="" required="required" placeholder="请输入市场价">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="price" class="col-sm-2 control-label">共享积分</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="share_integral" value="" placeholder="请输入共享积分">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="price" class="col-sm-2 control-label">运费</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="shipping" value="" placeholder="请输入运费">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="stock" class="col-sm-2 control-label">库存</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control"  name="stock" value="{{ $product_sku['stock'] or ''}}" placeholder="请输入库存">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="is_sale" class="col-sm-2 control-label">是否上架</label>
                            <div class="col-sm-10">
                                <select id="is_sale" class="form-control" name="is_sale">
                                    <option value="1">是</option>
                                    <option value="0">否</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-info add-product-sku-submit">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<form class="upload-form product-image-upload-form" method="post" enctype="multipart/form-data" style="display: none;">
    <input name="image"  accept="image/*" type="file" class="upload-file product-image-file product-image-upload-file" />
    <input type="hidden" name="product_id" value="{{ $product['id'] or '' }}">
    <input type="hidden" name="type" value="main">
</form>
<script type="text/template" id="product-image-template">
    <li style="display: inline-block;vertical-align: middle;margin-right: 10px;margin-bottom: 10px" data-name="{{ $image['image'] }}">
        <img src="{image}" width="100" style="cursor: default;">
        <p style="text-align: center;padding: 10px 0px"><input type="radio" name="image_check" value="{image_path}" /></p>
    </li>
</script>
@endsection
@section('scripts')
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
            $(".j_select_product_sku_image").on("click", function(){
                var sku_id = $(this).attr('data-id')
                var modal_id = $(this).attr('data-modal-id');
                var modal  = $("#" + modal_id);
                modal.find("[name='id']").val(sku_id);
                modal.modal();
            });
            $(".save_product_sku_image").on("submit", function(){
                var form = $(this);
                var image_check = form.find("[name=image_check]");
                if(image_check.size() == 0){
                    $.showMessage('请选择图片！');
                }
                var action = $(this).attr('action');
                var data = form.serializeObject();
                $.post(action, data, function(result){
                    if(result.code == '200'){
                        window.location.reload();
                    } else if(result.message != ''){
                        $.showMessage(result.message);
                    }
                }, 'json');
                return false;
            });
            $(".j_load_sku_edit").on("click", function(){
                var product_id = $(this).attr('data-id');
                var action = $(this).attr('data-action');
                var data = {'id': product_id};
                $.post(action, data, function(result){
                    if(result.code == '200'){
                        $("#edit_sku_box").html(result.data.view).show();
                        $("#edit_product_sku_modal").modal();
                    } else if(result.message != ''){
                        $.showMessage(result.message);
                    }
                }, 'json');
            });
            $("#edit_sku_box").on("submit", function(){
                var form = $(this).find('form');
                var action = form.attr('action');
                var data = form.serializeObject();
                $.post(action, data, function(result){
                    if(result.code == '200'){
                        window.location.reload();
                    } else if(result.message != ''){
                        $.showMessage(result.message);
                    }
                }, 'json');
            });
            $(".j_load_sku_add").on("click", function(){
                $("#add_product_sku_modal").modal();
            });
            $(".add-product-sku-submit").on("click", function(){
                var form = $('.add-product-sku-form');
                var action = form.attr('action');
                var data = form.serializeObject();
                $.post(action, data, function(result){
                    if(result.code == '200'){
                        window.location.reload();
                    } else if(result.message != ''){
                        $.showMessage(result.message);
                    }
                }, 'json');
            });
            //添加图片
            $(document).on("click", ".js-add-product-image", function(){
                $(".product-image-upload-file").click();
            });
            //添加图片
            $(".product-image-upload-file").on("change", function(event){
                var form = $('.product-image-upload-form');
                var elem = $(this);
                try{
                    var files = elem[0].files;
                    if(files && files.length > 0){
                        //Verify that the file type
                        if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg']) == -1){
                            mylayer.showTip(tipMessage.upload_image_format_tip, 5000, "error");
                            return false;
                        }
                        if(files[0].size){
                            var sm = files[0].size / (1024 * 1024);
                            if(sm > 5){
                                mylayer.showTip(tipMessage.upload_maximum_tip, 5000, "error");
                                return false;
                            }
                        }
                    }
                    self.productImageUload(form);
                }
                catch(e){}
            });
        };
        //图片上传
        app.productImageUload = function(form){
            var self = this;
            var formData = new FormData(form[0]);
            $.ajax({
                url: "/admin/api/product/addProductImage",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(result){
                    self.uploadCallback(form);
                    if(result.code == "200"){
                        var item = $("#product-image-template");
                        var data = [{
                            'image_id': result.data.image_id,
                            'image': result.data.image_link,
                            'image_path': result.data.image_path
                        }];
                        var box = $.tmeplate(item, data);
                        $(".product-image-list").append(box);
                    } else {
                        $.showMessage(result.message);
                    }
                },
                error: function(result){   
                    self.uploadCallback(form);
                    $.showMessage('系统错误！上传失败');
                }
            });
        };
        //上传后回调
        app.uploadCallback = function(form){
            var file_elem = form.find('input[type=file]');
            file_elem.after(file_elem.clone().val(""));   
            file_elem.remove();
        };
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection