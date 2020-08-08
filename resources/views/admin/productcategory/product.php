<?php
    use yii\helpers\Html;
?>
<?php $this->beginBlock('style') ?>
<link href="/media/admin/wangEditor/css/wangEditor.min.css" rel="stylesheet">
<?php $this->endBlock() ?>
<ul class="breadcrumb">
    <li><a href="/admin/productcategory">石雕管理</a></li>
    <?php if(!empty($parent_productcategory)): ?>
    <li><?= $parent_productcategory['name'] ?></li>
    <?php endif ?>
    <li class="active">产品</li>
</ul>
<div class="tree_panel clearfix">
    <div class="tree_aside">
        <div class="productcategory_tree"></div>
    </div>
    <div class="tree_content" style="min-width: 850px;">
        <div class="well well-sm">
            <span><a href="javascript:void(0)" class="btn btn-info add_product">添加产品</a></span>
        </div>
        <div class="well">
            <form class="form-inline search_form" role="form">
                <div class="form-group">
                    <label class="control-label" for="fullname">产品名称</label>
                    <input type="text" class="form-control" name="name" value="<?= isset($form['name']) ? $form['name'] : '' ?>" placeholder="请输入产品名称">
                </div>
                <input type="hidden" name="productcategory_id"  value="<?= isset($form['productcategory_id']) ? $form['productcategory_id'] : '' ?>" />
                <button type="submit" class="btn btn-info">查询</button>
            </form>
        </div>
        <div class="panel panel-info">
            <div class="panel-body">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>编号</th>
                            <th>分类</th>
                            <th>名称</th>
                            <th>规格</th>
                            <th>型号</th>
                            <th>品牌</th>
                            <th>材质</th>
                            <th>浏览数</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody class="product_table_list">
                        <?= $this->render('block/product_list', ['product_list' => $product_list]) ?>
                    </tbody>
                </table>
                <div class="well well-sm">
                    <?php if(!empty($pager)): ?>
                        <div class="text-center">
                            <?= $pager ?>
                       </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="productcategory_id" id="current_productcategory_id"  value="<?= isset($form['productcategory_id']) ? $form['productcategory_id'] : '' ?>" />
<div class="modal fade" id="save_product_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
    <div class="modal-dialog" style="width:800px;margin-top:20px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    产品信息
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal product_form" role="form" name="product_form" action="/admin/product/save" enctype="multipart/form-data" method="post">
                    <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
                    <input name="save_type" class="save_type" type="hidden" value="0">
                    <input name="id" class="form-control" type="hidden" />
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="nickname">所属分类</label>
                        <div class="col-sm-10">
                            <select name="product_category_id" class="form-control">
                                <option value="">请选择</option>
                                <?php if(!empty($productcategory_select_list)): ?>
                                <?php foreach ($productcategory_select_list as $key => $category): ?>
                                <option value="<?= $category['id'] ?>"><?php if($category['level'] == 0):?>&nbsp;<?php else: ?> <?php for($i = 0; $i <= $category['level']; $i++):?> &nbsp; <?php endfor?> <?php endif ?><?= $category['name'] ?>
                                </option>  
                                <?php endforeach ?>
                                <?php endif ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">名称</label>
                        <div class="col-sm-10">
                            <?= Html::input('text', 'name', '' , 
                            ['class' => 'form-control', 'placeholder' =>'请输入名称', 
                            'required' => 'required', 'maxlength'=>'255'
                            ])?>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label for="image" class="col-sm-2 control-label">图片信息</label>
                        <div class="col-sm-3 add_image_btn">
                            <input type="file" name="product_image" class="product_image_file" />
                        </div>
                        <div style="margin-top: 8px">
                            <span class="text-info"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label"></label>
                        <div class="col-sm-10">
                            <div style="background-color: #f9f9f9;display: none" class="product_image_box"></div>
                        </div>  
                    </div>
                    <div class="form-group">
                        <label for="image" class="col-sm-2 control-label">描述</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="product_description" name="description" rows="10" style="height: 300px"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="spec" class="col-sm-4 control-label">产品规格</label>
                            <div class="col-sm-8">
                                <?= Html::input('text', 'spec', '' , 
                                    ['class' => 'form-control', 'placeholder' =>'请输入规格', 
                                    'maxlength'=>'255'
                                ])?>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="name" class="col-sm-4 control-label">型号</label>
                            <div class="col-sm-8">
                                <?= Html::input('text', 'model_number', '' , 
                                ['class' => 'form-control', 'placeholder' =>'请输入型号', 
                                 'maxlength'=>'255'
                                ])?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="spec" class="col-sm-4 control-label">品牌</label>
                            <div class="col-sm-8">
                                <?= Html::input('text', 'brand', '' , 
                                    ['class' => 'form-control', 'placeholder' =>'请输入品牌', 
                                    'maxlength'=>'255'
                                ])?>
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="name" class="col-sm-4 control-label">材质</label>
                            <div class="col-sm-8">
                               <?= Html::input('text', 'material', '' , 
                                ['class' => 'form-control', 'placeholder' =>'请输入材质', 
                                 'maxlength'=>'255'
                                ])?>
                            </div>
                        </div>
                    </div>
                     <div class="form-group">
                        <label for="spec" class="col-sm-2 control-label">启用</label>
                        <div class="col-sm-10">
                           <select class="form-control" name="status">
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-center">
                        <input type="submit" class="btn btn-large btn-info btn-block" value="保存" />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<form id="editor_post_form" method="post" enctype="multipart/form-data" action="/admin/common/image2base64" style="display:none;">
    <input name="_csrf" type="hidden" value="<?= Yii::$app->request->csrfToken ?>">
    <input name="image" type="file" accept="image/*" id="editor_upload_post">
</form>
<?php $this->beginBlock('JS_END') ?>
    <script src="/media/admin/wangEditor/js/wangEditor.min.js"></script>
    <script src="/media/admin/js/plugin/asyncForm.js"></script>
    <script type="text/javascript">
    (function($){
        var app = {};
        app.init = function(){
            var self = this;
            var data = <?= json_encode($productcategory_tree) ?>;
            $('.productcategory_tree').jstree({
                'core' : {
                    "multiple": false,
                   'data': data
                }
            });
            $('.productcategory_tree').on("changed.jstree", function (e, data) {
                self.selectnode = data.node;
                if(data.node){
                    var id = data.node.id;
                    var data = {'productcategory_id': id};
                    $.showLoad();
                    var search_url = window.location.search;
                    if(id != null){
                        search_url = $.changeURLArg(search_url, 'productcategory_id', id);
                    } else {
                        search_url = $.delParam(search_url, 'productcategory_id');
                    }
                    window.location.href = search_url + window.location.hash;
                }
            });
            $(".product_form").validate({
                rules: {
                    productcategory_id: {
                        required: true
                    },
                    name: {
                        required: true,
                        maxlength: 255
                    },
                    model_number: {
                        maxlength: 255
                    },
                    brand: {
                        maxlength: 255
                    },
                    material: {
                        maxlength: 255
                    },
                    spec: {
                        maxlength: 255
                    },
                    status: {
                        required: true
                    }
                }
            });
            //显示添加
            $(".add_product").on("click",function(){
                $("#save_product_modal").modal({backdrop: 'static'});
                var form = $(".product_form");
                var save_type = form.find('.save_type').val();
                if(save_type != 0){
                    md_base.clearForm(form);
                    var current_productcategory_id = $("#current_productcategory_id").val();
                    form.find('[name="product_category_id"]').val(current_productcategory_id);
                }
                form.find('.save_type').val('0');
            }); 
            $(".product_form").on("submit", function () {
                var form = $(this);
                var submit_btn  = form.find('[type="submit"]');
                if(submit_btn.hasClass('disabled')){
                    return false;
                }
                if (form.valid()) {
                    var action = form.attr('action');
                    var data = form.serializeObject();
                    submit_btn.addClass('disabled');
                    var form_data = form.serializeObject();
                    var op = {
                        success: function(result){
                            submit_btn.removeClass('disabled');
                            if(result.code == "200"){
                                window.location.href = window.location.href;
                            } else {
                                $.showMessage(result.message);
                            }
                        },
                        error: function(index){
                            submit_btn.removeClass('disabled');
                            $.showMessage("error","哦啊,上传失败,网络错误或者系统错误!");
                        }
                    };
                    new asyncForm(form[0], op).submit(function(result, e) { 
                        op.success(result);
                    });
                }
                return false;
            });
            //上传文件改变后
            $(".product_image_file").on("change", function(event){
                //var form = $(this);
                var e = event || window.event;
                //var elem = $(e.target || e.srcElement);
                var elem = $(this);
                try{
                    if(elem[0].files){
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
                            var file = files[0];
                            var reader = new FileReader();
                            reader.onload = function(evt) {
                                var src = evt.target.result;
                                if(src){
                                    var product_image_item = '<div class="product_image_item"><img src="' + src + '" /></div>';
                                    $(".product_image_box").html(product_image_item).show();
                                } else {
                                    /*self.uploadFile(form, function(src){
                                        self.addProductImage(src);
                                    });*/
                                }
                            }
                            reader.readAsDataURL(file);
                        } 
                    } 
                } catch(e){}
            });
            this.description_editor = this.descriptionEditor('product_description');
            this.listEvent();
        };
        app.listEvent = function(){
            var self = this;
            //编辑
            $(".update_product").on("click", function(){
                var elem = $(this);
                $.showLoad();
                var id = elem.attr("data-id");
                var action = elem.attr('data-action');
                $.postAjax(action, {'id': id}, function(result) {
                    $.hideLoad();
                    if(result.data){
                        var form = $(".product_form");
                        md_base.loadForm(form, result.data);
                        $("#save_product_modal").modal({backdrop: 'static'});
                        form.find('.save_type').val('1');
                        self.description_editor.$txt.html(result.data.description);
                        var src = result.data.image;
                        if(src){
                            var product_image_item = '<div class="product_image_item"><img src="' + src + '" /></div>';
                            $(".product_image_box").html(product_image_item).show();
                        }
                        
                    } else {
                        $.showMessage(result.message);
                    }
                });
            });
            $(".remove_product").on("click", function(){
                var elem = $(this);
                $.showConfirm("您是否确定删除此产品？", function(){
                    var href = elem.attr('data-action');
                    window.location.href = href;
                })
            });
        };
        //编辑器功能
        app.descriptionEditor = function(name){
            var self = this;
            if(typeof wangEditor == "undefined" ){
                return null;
            }
            if($("#" + name).size() == 0){
                return null;
            }
            wangEditor.config.printLog = false;
            var ceditor = new wangEditor(name);
            // 自定义菜单
            ceditor.config.menus = ['img','head', 'italic', 'bold', 'underline', 'strikeThrough', 'fontsize','forecolor','bgcolor', 'alignleft','aligncenter','alignright','undo','redo', 'link','unlink', 'table', 'image'];
            // 使用英语
            //ceditor.config.lang = wangEditor.langs['en'];
            // 颜色
            ceditor.config.colors = {'#880000': 'Dark Red','#800080': 'Purple','#ff0000': 'Red','#ff00ff': 'Fresh pink','#000080': 'Navy Blue','#0000ff': 'Blue','#00ffff': 'Lake Blue','#008080': 'Blue-Green','#008000': 'Green','#808000': 'Olive','#00ff00': 'Light Green','#ffcc00': 'Orange','#808080': 'Gray','#c0c0c0': 'Silver','#000000': 'Black','#ffffff': 'White'};
            ceditor.create();
            $(".wangEditor-menu-container .menu-item").first().off("click").on("click", function(){
                $("#editor_upload_post").click();
                return false;
            });
            $("#editor_upload_post").off("change").on("change", function(){
                var file = document.getElementById(this.id).files[0];
                //Verify that the file type
                if(file.type.indexOf("image") == "-1"){
                    mylayer.showMessage("error", '请上传图片,检查格式');
                    return false;
                } else {
                    self.editorupload(ceditor);
                }
            });
            return ceditor;
        };
        app.editorupload = function(editor){
            var self = this;
            var op = {
                success: function(result){
                    if(result.code == "200"){
                        var imgsrc = result.imgsrc ? result.imgsrc : '';
                        editor.command(null, 'insertHtml', '<img src="' + imgsrc + '" style="max-width:100%;"/>');
                    } else {
                        if(result.result){
                            mylayer.showMessage("error", result.result);
                        }
                    }
                },
                error: function(){
                    mylayer.showMessage("error", "Sorry,please try it again!");
                }
            };
            var form = document.getElementById("editor_post_form");
            new asyncForm(form, op).submit(function(result, e){ 
                op.success(result);
            });
        };
        app.loadProductImage = function(image_list){
            var html = '';
            $.each(image_list, function(key, item){
                html += '<div class="product_image_item" data-id="' + item['id'] +'"><img src="' + item['image'] + '" />';
            });
            $(".product_image_box").html(html);
        };
        $(function(){
            app.init();
        });
    })(jQuery);
    </script>
<?php $this->endBlock() ?>
