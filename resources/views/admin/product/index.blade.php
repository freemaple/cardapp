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
@if(isset($form['is_self']) && $form['is_self'] != '0')
<div class="well well-sm">
    <ul class="nav nav-pills">
        <a href="{{ route('admin_product_add') }}" class="btn btn-info" data-modal-id="upload_product_modal"><span class="glyphicon glyphicon-plus"></span>上传产品</a>
        <a href="javascript:void(0)" class="btn btn-info j_modal_link" data-modal-id="upload_edit_product_modal" style="display: none"><span class="glyphicon glyphicon-edit"></span>更新产品</a>
    </ul>
</div>
@endif
<div class="panel panel-info">
    <div class="well">
        <form class="form-inline" role="form">
            <div class="form-group">
                <label for="name" class="control-label">所属分类</label>
                <select name="category_id" class="form-control">
                    <option value="">请选择</option>
                    @if(!empty($productCategory_select_list))
                    @foreach ($productCategory_select_list as $key => $category)
                    <option value="{{ $category['id'] }}" @if(isset($form['category_id']) && $form['category_id'] == $category['id']) selected="selected" @endif>@if($category['level'] == 0)&nbsp;@else @for($i = 0; $i <= $category['level']; $i++) &nbsp; @endfor @endif {{$category['name'] }}</option>  
                    @endforeach ?>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label class="control-label" for="name">名称</label>
                <input type="text" class="form-control" name="name" value="{{ $form['name'] or '' }}" size="10" placeholder="请输入名称">
            </div>
            <div class="form-group">
                <label class="control-label" for="spu">spu</label>
                <input type="text" class="form-control"  name="spu" value="{{ $form['spu'] or '' }}" size="10" placeholder="请输入spu">
            </div>
            <div class="form-group">
                <label class="control-label" for="is_sale">上架状态</label>
                <select name="is_sale" class="form-control">
                    <option value="">全部</option>
                    <option value="1" @if(isset($form['is_sale']) && $form['is_sale'] == '1') selected="selected" @endif>上架</option>
                    <option value="0" @if(isset($form['is_sale']) && $form['is_sale'] === '0') selected="selected" @endif>下架</option>
                </select>
            </div>
            <div class="form-group">
                <label class="control-label">创建时间从</label>
                <input type="text" class="start_date form-control laydate-icon" name="start_date" size="14" value="{{ $login_start_date or '' }}"  />
            </div>
            <div class="form-group">
               <label class="control-label">至</label>
                <input type="text" class="end_date form-control laydate-icon" name="end_date" size="14" value="{{ $login_end_date or '' }}" />
            </div>
            <button type="submit" class="btn btn-info">查询</button>
            <div class="form-group" style="margin-top: 15px">
                <div class="form-group">
                    <label class="control-label" for="is_sale">排序</label>
                    <input type="radio" name="sort" checked="checked" />创建时间
                    <input type="radio" name="sort" />更新时间
                    <input type="radio" name="sort" />销量
                </div>
            </div>
            <input type="hidden" name="is_self" value="{{ $form['is_self'] or '1' }}">
            <input type="hidden" name="is_add_gift" value="{{ $form['is_add_gift'] or '0' }}">
        </form>
    </div>
    @if(!empty($pager))
        <div class="clearfix pager_block">
            <div class="item_status pull-left">
                共 {{ $product_list['total'] }} 个产品，当前 {{ $product_list['from'] }}-{{ $product_list['to'] }}
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
                <th>店铺</th>
                <th width="120">基本信息</th>
                <th>spu</th>
                <th width="100">图片</th>
                <th>销量</th>
                <th>是否上架</th>
                <th>规格料号</th>
                <th>创建人</th>
                <th>时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            @if(!empty($product_list['data']))
                @foreach($product_list['data'] as $key => $list)
                    <tr>
                        <td><input type="checkbox" class="row_check" data-id="{{ $list['id'] }}" /></td>
                        <td>{{ $list['id'] }}</td>
                        <td>
                            @if($list['is_self'] == '1') 自营 @elseif(!empty($list['store_info']))
                             <a target="_blank" href="{{ route('store_view', $list['store_info']['id']) }}">{{ $list['store_info']['name'] or '' }}</a> @endif</td>
                        <td>
                            <p>名称：{{ $list['name'] }}</p>
                            <p>分类：{{ $list['categoryinfo']['name'] or '' }}</p>
                            <p>产品类型： {{ $list['is_gift'] == '1' ? '礼包产品' : '普通产品' }}</p>
                        </td>
                        <td>{{ $list['spu'] }}</td>
                        <td width="100">
                            <img src="{{ HelperImage::storagePath(isset($list['image']) ? $list['image'] : '') }}?v={{ $list['updated_at'] }}" width="100" />
                            <p style="text-align: center;padding: 10px 0px"></p>
                        </td>
                        <td>{{ $list['sales_numbers'] }}</td>
                        <td>{{ $list['is_sale'] == "1" ? "上架" :"已下架" }}</td>
                        <td>
                            @if(!empty($list['sku_list']))
                            @foreach($list['sku_list'] as $skey => $sku)
                            <p>
                                <span style="display: inline-block;margin-right: 10px">{{ $sku['sku'] }}</span> <span style="display: inline-block;margin-right: 10px">${{ $sku['price'] }}</span> <span style="display: inline-block;margin-right: 10px">库存：{{ $sku['stock'] }}</span>
                                <span style="display: inline-block;margin-right: 10px">{{ $sku['is_sale'] == "1" ? "上架" :"已下架" }}</span>
                            </p>
                            @endforeach
                            @endif
                        </td>
                        <td>
                            @if($list['is_self'] == '1') 
                                {{ $list['admin_name'] or '' }} 
                            @elseif(!empty($list['user_info']))
                                <a href="{{ route('admin_customer_info', $list['user_info']['id']) }}" target="_blank">{{ $list['user_info']['fullname'] or '' }}</a>
                            @endif
                            
                        </td>
                        <td>
                            <p>创建时间：{{ $list['created_at'] }}</p>
                            <p>更新时间：{{ $list['updated_at'] }}</p>
                        </td>
                        <td>
                            @if($list['is_self'] == '1')
                                <a type="button" class="btn btn-info j_edit_product_link"  data-load-action="/admin/api/product/load" data-id="{{ $list['id'] }}" data-modal-id="edit_product_modal">
                                    编辑
                                </a>
                            @endif
                            <a class="btn btn-info" target="_blank" href="{{ route('admin_product_sku', $list['id']) }}">
                                规格料号
                            </a>
                            @if($is_add_gift && $list['is_self'] == '1' && $list['is_gift'] != 1)
                            <a class="btn btn-info" target="_blank" href="{{ route('admin_product_gift_add', ['product_id' => $list['id']]) }}">
                                设置为礼品
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
<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="upload_product_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal ajax_form" role="form" action="/admin/api/product/upload" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        上传产品
                    </h4>
                </div>
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="edit_type" name="add" />
                    <div class="form-group">
                        <label for="name" class="col-sm-4 control-label">文件</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="file" name="file" required="required" maxlength="255" />
                            <a href="/template/product.xlsx">模板下载</a>
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
<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="upload_edit_product_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal ajax_form" role="form" action="/admin/api/product/upload" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        批量更新产品
                    </h4>
                </div>
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="edit_type" value="edit" />
                    <div class="form-group">
                        <label for="name" class="col-sm-4 control-label">文件</label>
                        <div class="col-sm-8">
                            <input class="form-control" type="file" name="file" required="required" maxlength="255" />
                            <a href="/template/update_product.xlsx">批量更新产品模板下载</a>
                            <a href="/template/update_product_sku.xlsx">批量更新产品sku模板下载</a>
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
<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="edit_product_modal">
    <div class="modal-dialog" style="width: 1100px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    编辑产品
                </h4>
            </div>
            <form class="form-horizontal save_product" role="form" action="/admin/api/product/update" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="">
                    <div class="panel panel-default">
                        <div class="panel-heading">基本信息</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Spu</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="input" name="spu" disabled="disabled" required="required" maxlength="255" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="input" name="name" required="required" maxlength="255" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="is_sale" class="col-sm-2 control-label">商品分类<span class="text-red">*</span></label>
                                <div class="col-sm-10">
                                    <select name="category_id" class="form-control category_id">
                                        <option>请选择</option>
                                        @if(!empty($productCategory_select_list))
                                        @foreach($productCategory_select_list as $ckey => $category)
                                        <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">电话号码<span class="text-red">*</span></label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="input" name="service_phone" required="required" maxlength="255" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">描述</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" rows="6" name="description"  maxlength="255"></textarea>
                                </div>
                            </div>
                            <div>
                                <video
                                height='200'
                                id="my-player"
                                class="video-js product_edit_video"
                                controls
                                preload="auto"
                                poster="{{ Helper::asset_url('/media/images/poster.png') }}" 
                                style="width: 100%;">
                                    <source src="" class="video_src" type="video/mp4"></source>
                                </video>
                                <a href="javascript:void(0)" class="j_upload_product_video">修改视频</a>
                            </div>
                            <div class="form-group">
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
                    <div class="panel panel-default">
                        <div class="panel-heading">描述图</div>
                        <div class="panel-body">
                            <div class="">
                                <a href="javascript:void(0)" class="btn btn-info j_upload_product_image">添加图片</a>
                            </div>
                            <div>
                                <ul class="image_list" style="padding: 20px 0px"></ul>
                                <div class="no_image_box">
                                    <img src="https://www.dianxiaomi.com/static/img/kong.png" style="cursor: default;">
                                </div>
                            </div>
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
<form class="image_upload_form" action="/admin/api/common/image2base64" enctype="multipart/form-data" method="post" style="display: none;" onsubmit="return false">
    {!! csrf_field() !!}
    <input type="file" class="image_upload_file" accept="image/*" name="image" required="required" />
</form>
<form class="video_upload_form" action="/admin/api/product/uploadvideo" enctype="multipart/form-data" method="post" style="display: none;" onsubmit="return false">
    {!! csrf_field() !!}
    <input type="hidden" class="file_product_id"  name="product_id" required="required" />
    <input type="file" class="video_upload_file"  name="video" required="required" />
</form>
<div id="select_spu_image_box"></div>
@endsection
@section('scripts')
{!! App\Assets\Admin::script('admin/scripts/plugin/jstree.min.js') !!}
{!! App\Assets\Admin::script('admin/scripts/plugin/sortable.min.js') !!}
{!! App\Assets\Admin::script('admin/scripts/plugin/asyncForm.js') !!}
<script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
            var image_list = $(".image_list")[0];
            Sortable.create(image_list)
            $(".j_upload_product_image").on("click", function(){
                $(".image_upload_file").click();
            });
            //上传文件改变后
            $(".image_upload_form").on("change", function(event){
                var form = $(this)
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                if(elem.hasClass('image_upload_file')){
                    try{
                        var files = elem[0].files;
                        if(files && files.length > 0){
                            //Verify that the file type
                            if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/jpg', 'image/jpeg']) == -1){
                                $.showMessage('No, please check it,you can only upload png/jpg/jpeg image');
                                return false;
                            }
                            if(files[0].size){
                                var sm = files[0].size / (1024 * 1024);
                                if(sm > 10){
                                    $.showMessage('We currently support a maximum image size of 10 MB');
                                    return false;
                                }
                            }
                            self.artUload({'form': form});
                        }
                    }
                    catch(e){}
                }
            });
            //上传文件改变后
            $(".image_list").on("click", function(event){
                var form = $(this)
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                if(elem.hasClass('remove_image_item')){
                    var remove_image_item = elem;
                    remove_image_item.closest('.product_image_item').remove();
                    if($(".product_image_item").size() == 0){
                        $(".no_image_box").show();
                    } 
                }
            });
            $(".j_edit_product_link").on("click", function(){
                var modal_id = $(this).attr('data-modal-id');
                var action = $(this).attr('data-load-action');
                var id = $(this).attr('data-id');
                $.post(action, {'id': id}, function(result){
                    if(result.code == '200'){
                        var data = result.data;
                        var modal = $("#" + modal_id)
                        var form = modal.find('form');
                        $.each(data, function(key, value){
                            if(key != 'video'){
                                form.find('[name="' + key +'"]').val(value);
                                form.find('[data-name="' + key +'"]').text(value);
                            } else {
                               form.find('[name="' + key +'"]').val(''); 
                               var layer = $("#my-player");
                               var video_src = form.find('.video_src').prop('src', value);
                               var video_src_c = video_src.clone();
                               video_src_c.appendTo(layer);
                               video_src.remove();
                                if(value){
                                    $("#my-player").show();
                                } else {
                                    $("#my-player").hide();
                                }
                            }
                            if(key == 'category_id'){
                                form.find('.category_id').find('option[value="' + value + '"]').prop('selected', 'selected');
                            }
                        });
                        if(data.image && data.image.length > 0){
                            var imginfo = self.imageInfo(data.image);
                            form.find(".image_list").html(imginfo);
                            form.find(".no_image_box").hide();
                        } else {
                            form.find(".no_image_box").show();
                        }
                        $(".file_product_id").val(id);
                        modal.modal();
                    }
                }, 'json')
            });
            $(".save_product").on("submit", function(){
                var form = $(this);
                if(form.valid()){
                    var action = $(this).attr('action');
                    var data = form.serializeObject();
                    var product_images = [];
                    var image_list = form.find(".product_image_item");
                    if(image_list.size() > 0){
                        image_list.each(function(){
                            var image_data = {};
                            var image = $(this).attr('data-image');
                            image_data['image'] = image;
                            var imgsrc = $(this).find("img").attr('src');
                            if(imgsrc){
                                image_data['imgsrc'] = imgsrc;
                            }
                            product_images.push(image_data);
                        });
                    } else {
                        product_images = null;
                    }
                    data.product_images = product_images;
                    $.post(action, data, function(result){
                        if(result.code == '200'){
                            window.location.reload();
                        } else if(result.message != ''){
                            $.showMessage(result.message);
                        }
                    }, 'json')
                }
                return false;
            });
            $(".j_upload_product_video").on("click", function(){
                $(".video_upload_file").click();
            });
            //上传文件改变后
            $(".video_upload_form").on("change", function(event){
                var form = $(this)
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                if(elem.hasClass('video_upload_file')){
                    try{
                        var files = elem[0].files;
                        if(files && files.length > 0){
                            if(files[0].size){
                                var sm = files[0].size / (1024 * 1024);
                                if(sm > 30){
                                    $.showMessage('不能超过30 MB');
                                    return false;
                                }
                            }
                            self.videoUload({'form': form});
                        }
                    }
                    catch(e){}
                }
            });
        };
        //图案上传
        app.artUload = function(option){
            var self = this;
            var form = option.form ? $(option.form) : false;
            if(form && form.size() > 0){
                var op = {
                    success: function(result){
                        if(typeof result.imgsrc != 'undefined'){
                            var art_upload_file = form.find(".image_upload_file").val('');
                            art_upload_file.replaceWith(art_upload_file.clone());
                            var data = {};
                            var imgsrc = result.imgsrc ? result.imgsrc : '';
                            if(imgsrc){
                                var data = [{'imgsrc': imgsrc}];
                                var imginfo = self.imageInfo(data);
                                var modal_form = $(".save_product:visible");
                                if(modal_form.size() > 0){
                                    modal_form.find(".image_list").append(imginfo);
                                    modal_form.find(".no_image_box").hide();
                                }
                            }
                        }
                        else{
                            if(result.message){
                                $.showMessage(result.message);
                            }
                        }
                    },
                    error: function(index){
                        var art_upload_file = form.find(".image_upload_file").val('');
                        art_upload_file.replaceWith(art_upload_file.clone());
                        $.showMessage("error","哦啊,上传失败,网络错误或者系统错误!");
                    }
                };
                new asyncForm(form[0], op).submit(function(result, e){ 
                    op.success(result);
                });
            }
        };
        //图案上传
        app.videoUload = function(option){
            var self = this;
            var form = option.form ? $(option.form) : false;
            if(form && form.size() > 0){
                var op = {
                    success: function(result){
                        if(result.code == '200'){
                            var video = result.data.video ? result.data.video : '';
                            if(video){
                               $(".video_src").attr('src', video);
                               $(".product_edit_video").show();
                            }
                        } else if(result.message != ''){
                            $.showMessage(result.message);
                        } else {
                            $.showMessage("哦啊,上传失败,网络错误或者系统错误!");
                        }
                    },
                    error: function(index){
                        var video_upload_file = form.find(".video_upload_file").val('');
                        video_upload_file.replaceWith(video_upload_file.clone());
                        $.showMessage("哦啊,上传失败,网络错误或者系统错误!");
                    }
                };
                new asyncForm(form[0], op).submit(function(result, e){ 
                    op.success(result);
                });
            }
        };
        app.imageInfo = function(data){
            var imginfo = ''
            $.each(data, function(ikey, image_data){
                imginfo += "<li class='item product_image_item' data-id='" + (image_data.id ? image_data.id : '')  + "' data-image='" + (image_data.image ? image_data.image : '')  + "'><img  width='100' src='" + image_data.imgsrc + "' /><a href='javascript:void(0)'' title='close' class='close_btn remove_image_item'>×</a></li>";
            });
            return imginfo;
        };
        $(function(){
            app.init();
        });
    })(jQuery);
</script>
@endsection