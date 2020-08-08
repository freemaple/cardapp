@extends('layouts.app')

@section('header_title') 微链接 @endsection

@section('styles')
<style type="text/css">
    .image-item {
        display: inline-block;
        width: 25%;
        font-size: 0px;
        position: relative;
        min-height: 100px;
        background-color: #ff9800;
        overflow: hidden;
        border: 1px solid #e2e2e2;
        margin-bottom: 20px;
        font-size: 0px;
        margin-right: -4px;
    }
    .image-item-add {
        height: 150px;
    }
    .image-item-add .add-box {
        position: absolute;
        top: 50%;
        margin-top: -30px;
        text-align: center;
        color: #fff;
        font-size: 40px;
        left: 0px;
        width: 100%
    }
    
    .image-item-add a {
        color: #fff
    }
    .image-item img {
        display: block;
        margin: auto;
        width: auto;
        height: 150px;
    }
    .remove-item {
        position: absolute;
        right: 0px;
        top: 0px;
        border: 1px solid #e2e2e2;
        color: #fff;
        font-size: 12px;
        width: 34px;
        height: 34px;
        line-height: 34px;
        text-align: center;
        border-radius: 50%
    }
</style>
@endsection

@section('header')
	<div class="mobile-header clearfix">
	    <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="{{ Helper::route('account_card_custom', $card['card_number']) }}"><span class="iconfont icon-back"></span></a>
            </div>
	        <div class="mobile-header-title">相册</div>
	    </div>
	</div>
@endsection

@section('content')
<div class="account-warp">
	<ul class="image-list">
        @foreach($card_albums as $ikey => $card_album)
		<li class="image-item album-image-item" data-id="{{ $card_album['id'] }}">
			<div class="image">
				<img src="{{ HelperImage::storagePath($card_album['image']) }}"  />
			</div>
            <a class="remove-item js-remove-card-album" data-id="{{ $card_album['id'] }}" data-card-id="{{ $card['id'] }}" title="删除">删除</a>
		</li>
        @endforeach
        <li class="image-item image-item-add js-add-card-album">
            <span class="add-box">+</span>
        </li>
	</ul>
</div>
<form class="upload-form album-upload-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="card_id" value="{{ $card['id'] }}" />
    <input name="image" accept="image/*" type="file" class="upload-file album-upload-file" />
</form>
@endsection

@section('footer')
<div class="mobile-footer">
	<div class="btn-group text-center">
        <a href="{{ Helper::route('account_card_custom', $card['card_number']) }}" class="btn btn-primary btn-block">确定</a>  
    </div>
</div>
@endsection

@section('scripts')
<script>
    //基础加载
require(['zepto', 'base', 'mylayer', 'validate'], function ($, md_base, mylayer, validate) {
    var app = {
        init: function(){
            var self = this;
            //添加相册
            $(".js-add-card-album").on("click", function(){
                var size = $(".album-image-item").size();
                if(size >=12){
                    mylayer.showTip('内存不足了，只能添加12张！', 5000, "error");
                    return false;
                }
                $(".album-upload-file").click();
            })
            //选择图片后上传预览
            $(".album-upload-file").on("change", function(event){
                var form = $('.album-upload-form');
                var elem = $(this);
                try{
                    var files = elem[0].files;
                    if(files && files.length > 0){
                        //Verify that the file type
                        if(!files[0].type || $.inArray(files[0].type, ['image/png', 'image/gif', 'image/jpg', 'image/jpeg']) == -1){
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
                    self.cardAlbumUload(form);
                }
                catch(e){}
            });
            //删除名片相册
            $(".js-remove-card-album").on("click", function(){
                var card_id = $(this).attr('data-card-id');
                var card_album_id = $(this).attr('data-id');
                $.ajaxPost('/api/card/album/remove', {'card_id': card_id, 'card_album_id': card_album_id}, function(result){
                    if(result.code == 'Success'){
                        window.location.reload();
                    }
                });
            });
            $(".js-back-card").on("click", function(){

            })
        },
        //图片上传
        cardAlbumUload: function(form){
            var self = this;
            var formData = new FormData(form[0]);
            mylayer.showLoad(true);
            $.ajax({
                url: "/api/card/addCardAlbum",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(result){
                    mylayer.hideLoad();
                    self.uploadCallback(form);
                    if(result.code == "Success"){
                        window.location.reload();
                    } else {
                        $.showRequestError(result);
                    }
                },
                error: function(result){   
                    mylayer.hideLoad();
                    self.uploadCallback(form);
                    $.showRequestError(result);
                }
            });
        },
        //上传后回调
        uploadCallback: function(form){
            var file_elem = form.find('input[type=file]');
            file_elem.after(file_elem.clone().val(""));   
            file_elem.remove();
        }
    }
    var tipMessage = {
        upload_image_format_tip: '请选择png、jpg、jpeg格式图片！',
        upload_maximum_tip: '图片文件不能超过5M'
    }
    if(typeof app.init == 'function') {
        $(function () {
            app.init();
        });
    }
}); 

</script>
@endsection

