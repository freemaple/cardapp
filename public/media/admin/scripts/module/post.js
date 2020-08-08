var md_post = (function ($) { 
     var app = {
        init: function(){
            var self = this;
            this.descriptionEditor('post-description');
        },
        //编辑器功能
        descriptionEditor: function(name){
            if(typeof wangEditor == "undefined" ){
                return null;
            }
            if($("#" + name).size() == 0){
                return null;
            }
            wangEditor.config.printLog = false;
            var ceditor = new wangEditor(name);
            // 自定义菜单
            ceditor.config.menus = ['img', 'head',  // 标题
            'bold',  // 粗体
            'fontSize',  // 字号
            'fontName',  // 字体
            'forecolor',
            'bgcolor',
            'italic',  // 斜体
            'underline',  // 下划线
            'strikeThrough',  // 删除线
            'alignleft','aligncenter','alignright','undo',
            'redo',  'table', 'list', 'emoticon'];
            // 使用英语
            ceditor.config.lang = wangEditor.langs['en'];
            // 颜色
            ceditor.config.colors = {'#880000': 'Dark Red','#800080': 'Purple','#ff0000': 'Red','#ff00ff': 'Fresh pink','#000080': 'Navy Blue','#0000ff': 'Blue','#00ffff': 'Lake Blue','#008080': 'Blue-Green','#008000': 'Green','#808000': 'Olive','#00ff00': 'Light Green','#ffcc00': 'Orange','#808080': 'Gray','#c0c0c0': 'Silver','#000000': 'Black','#ffffff': 'White'};
            ceditor.config.uploadImgShowBase64 = true
            ceditor.create();
            $(".wangeditor-menu-img-picture").closest('.menu-item').off("click").on("click", function(){
                $("#editor_upload_post").click();
                return false;
            });
            $(".wangeditor-menu-img-play").closest('.menu-item').off("click").on("click", function(){
                var content = $("#video-link-template").html();
                mylayer.init({
                    content: content,
                    close: false,
                    class_name: "layer-videolink",
                    position: 'center'
                });
                return false;
            });
            //选择图片后上传预览
            $("#editor_upload_post").on("change", function(event){
                var form = $(this);
                var e = event || window.event;
                var elem = $(e.target || e.srcElement);
                 try{
                    var files = elem[0].files;
                    if(files && files.length > 0){
                        //Verify that the file type
                        if(!files[0].type || $.inArray(files[0].type, ['image/png','image/jpg', 'image/gif', 'image/jpeg']) == -1){
                            $.showMessage('请上传图片格式');
                            return false;
                        }
                        if(files[0].size){
                            var sm = files[0].size / (1024 * 1024);
                            if(sm > 5){
                                $.showMessage('图片不能超过5M');
                                return false;
                            }
                        }
                    }
                    var file = files[0];
                    var reader = new FileReader();
                    // 将文件以Data URL形式进行读入页面
                    reader.readAsDataURL(file);
                    reader.onload = function(f){
                        var src = this.result;
                        ceditor.command(null, 'insertHtml', '<img src="' + src + '" style="max-width:100%;"/>');
                    }
                }
                catch(e){}
            });
            this.ceditor = ceditor;
            return ceditor;
        },
        //图片上传
        editorupload:function(editor){
            var _this = this;
            var op = {
                success:function(result){
                    if(result.code=="200"){
                        var imgsrc=result.info.img_src?result.info.img_src:'';
                        editor.command(null, 'insertHtml', '<img src="' + imgsrc + '" style="max-width:100%;"/>');
                    }
                    else{
                        if(result.result){
                            mylayer.showMessage("error",result.result);
                        }
                    }
                },
                error:function(){
                    mylayer.showMessage("error","Sorry,please try it again!");
                }
            };
            var form = document.getElementById("editor_post_form");
            $("#upload_file_name").val("editor_upload_file");
            new asyncForm(form, op).submit(function(result, e){ 
                op.success(result);
            });
        }
    }
    return app;
})(jQuery);
if (typeof md_post.init == "function") {
    $(function () {
        md_post.init();
    });
}
