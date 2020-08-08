@extends('layouts.app')

@section('title') {{ $title }} @endsection

@section('styles')
<style type="text/css">
    body {
        background-color: #fff;
        padding: 10px
    }
</style>
@endsection

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-back">
                <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
            </div>
            <div class="mobile-header-title">{{ str_limit($title, 20) }}</div>
        </div>
    </div>
@endsection

@section('content')
<div class="contact_wrapper">
    <form name="contact-form" class="js-contact-form" data-action="/api/feedback">
        <div style="text-align:center;padding: 10px 0px;color: #fe5430">感谢您对我们的关心与支持！</div>
        <div class="form-group">
            <div class="form-group-label">姓名</div>
            <input type="text" name="fullname" placeholder="姓名" class="form-control form_text" />
        </div>
        <div class="form-group">
            <div class="form-group-label">联系电话</div>
            <input type="text" name="phone" placeholder="联系电话" class="form-control form_text" />
        </div>
        <div class="form-group">
            <div class="form-group-label">内容</div>
            <textarea  placeholder="请描述您的建议和问题" class="form-control form-textarea" name="content" rows="5"></textarea>
        </div>
        <div class="text_align btn_container">
            <input type="submit" class="btn btn-primary btn-block submit_feedback" value="提交"  />
        </div>
    </form>
</div>
@endsection

@section('scripts')
    <script type="text/javascript">
        //基础加载
        require(['zepto', 'base', 'mylayer', 'validate'], function ($, md_base, mylayer, validate) {
            //地址维护
            var app = {
                init: function(){
                    var self = this;
                    //设置表单验证
                    var validator = new FormValidator('contact-form', self.formRule, function(errors, event) {
                        var form = $(event.target);
                        form.find('.errormsg').html('');
                        if (errors.length > 0) {
                            self.showValidatorError(errors, form);
                            return false;
                        }
                        var action = form.attr('data-action');
                        var layer = mylayer.showLoad(true);
                        var data = form.serializeObject();
                        $.ajaxPost(action, data, function(result){
                            mylayer.hideLoad();
                            if(result.code == 'Success'){
                                mylayer.showTip(result.message, 3000, 'success');
                                form[0].reset();
                            } else {
                                $.showRequestError(result);
                            }
                        }, function(result){
                            mylayer.hideLoad();
                            $.showRequestError(result);
                        });
                    });
                    $(".js-contact-form").on("submit", function(){
                        return false;
                    })
                },
                //地址验证规则
                formRule: [
                    {
                        name: 'fullname',
                        rules: 'required|is_letter|max_length[50]',
                        message: {
                            required: '请输入姓名！'
                        }
                    }, {
                        name: 'phone',
                        rules: 'required|numeric|max_length[50]',
                        message: {
                            required: '请输入手机号码！',
                            numeric: '请输入手机号码！'
                        }
                    }, {
                        name: 'content',
                        rules: 'required',
                        message: {
                            required: '请输入内容！'
                        }
                    }
                ],
                //验证提示
                showValidatorError: function(errors, form){
                    for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
                        var elem = $(errors[i].element);
                        if(elem.next('.errormsg').size() == 0){
                            elem.after('<div class="errormsg"></div>');
                        }
                        elem.next('.errormsg').html(errors[i].message);
                    }
                    var top = form.offset().top;
                    window.scrollTo(0, top);
                },
                //设置地址表单数据
                setFormData: function(form, data) {
                    var obj = form.find("[name]");
                    var name;
                    obj.each(function(item){
                        name = $(this).attr("name");
                        if($(this).hasClass('check')){
                            if(data[name]){
                                $(this).prop('checked', 'checked');
                            } else{
                                $(this).removeAttr('checked');
                            }
                        }
                        $(this).val(data[name] ? data[name] : '');
                    });
                }
            };
            if(typeof app.init == 'function') {
                $(function () {
                    app.init();
                });
            }
        }); 
    </script>
@endsection

