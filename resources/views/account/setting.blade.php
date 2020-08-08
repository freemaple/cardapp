@extends('layouts.app')

@section('header')
<div class="mobile-header clearfix">
    <div class="mobile-header-box clearfix">
        <div class="mobile-header-back">
            <a href="javascript:void(0)" class="js-link-back"><span class="iconfont icon-back"></span></a>
        </div>
        <div class="mobile-header-title">{{ $title }}</div>
    </div>
</div>
@endsection
@section('content')
<div class="account-setting-content">
  	<div class="box-tab">
    	<div class="tab-menu clearfix">
			<ul class="menu-box-list clearfix">
				<li class="tab-item menu-box-item @if(!$tab) current @endif"><a>基本信息</a></li>
				<li class="tab-item menu-box-item @if($tab == 'password') current @endif"><a>登录密码修改</a></li>
				<li class="tab-item menu-box-item @if($tab == 'transaction_password') current @endif"><a>交易密码设置</a></li>
			</ul>
      	</div>
        <div class="tab-content">
          	<div class="tab-content-item account-setting-panel @if(!$tab) current @endif">
	            <div class="block-panel">
	              	<div class="block-panel-content"> 
		                <div class="setting-content-box">
		                    <form class="account-setting-form" name="account-setting-form" method="post" data-action="/api/account/changeinfo" onsubmit="return false">
			                    <div class="form-group-list clearfix">
			                        <div class="form-group">
			                        	<div class="form-group-label">姓名</div>
			                        	<input type="text" maxlength="8" class="form-control" name="fullname" maxlength="50" value="{{ $user['fullname'] or ''}}" />
			                        </div>
			                        <div class="form-group">
			                        	<div class="form-group-label">性别</div>
										<select type="text" class="form-control" name="gender">
											<option value="0" @if($user['gender'] == '0') selected="selected" @endif>保密</option>
											<option value="1" @if($user['gender'] == '1') selected="selected" @endif>男</option>
											<option value="2" @if($user['gender'] == '2') selected="selected" @endif>女</option>
										</select>
			                        </div>
			                    </div>
			                    <div class="form-group">
		                        	<div class="form-group-label">电子邮箱(Email)</div>
		                        	<input type="text" maxlength="50" class="form-control" name="email" maxlength="50" value="{{ $user['email'] or ''}}" />
		                        	<span class="tip-info" style="font-size: 12px">如：***@qq.com、***@163.com</span>
			                    </div>
			                    <div class="form-group">
			                        <div class="form-group-label">微信号</div>
			                        <input type="text" maxlength="50" class="form-control" name="weixin" maxlength="50" value="{{ $user['weixin'] or ''}}" />
			                    </div>
			                    <div class="form-group">
									<div class="form-group-label">上传微信二维码</div>
									<div class="resultImgbox js-change-qt" style="position: relative;min-height: 200px;width: 100%;background-color: #eee;text-align: center;padding: 20px 0px">
										<span class="closebtn"><i class="ic-pubfont ic-close"></i></span>
										<img src="{{ HelperImage::storagePath($user['weixin_qr']) }}" class="qr-image" style="width: 150px" />
									</div>
								</div>
								<div style="display: none;">
									<div class="form-group">
										<div class="form-group-label">自定义名称</div>
										<input type="text" class="form-control" name="signature_title" maxlength="255"  value="{{ $user['signature_title'] }}" placeholder="请填写自定义名称">
									</div>
									<div class="form-group">
										<div class="form-group-label">自定义内容</div>
										<input type="text" class="form-control" name="signature_content" maxlength="255" value="{{ $user['signature_content'] }}" placeholder="请填写自定义内容">
									</div>
								</div>
		                        <div class="btn-group text-center">  
		                            <input type="submit" class="btn btn-primary btn-block" value="保存" />
		                        </div>
		                   	</form>
		                </div>
	              	</div>
	            </div>
          	</div>
            <div class="tab-content-item account-setting-panel setting-content-box @if($tab == 'password') current @endif" >
                <div class="block_panel">
                 	<div class="block_panel_content"> 
                    	<form class="alert-password-form" name="alert-password-form" data-action="/api/account/changepwd" onsubmit="return false">
	                        <div class="form-group">
	                        	<div class="form-group_label">当前密码</div>
	                        	<input type="password" class="form-control" required="required" name="password_old" />
	                        </div>
	                        <div class="form-group">
	                           <div class="form-group_label">新密码</div>
	                           <input type="password" class="form-control" required="required" maxlength="50" name="password"  />
	                        </div>
	                        <div class="form-group">
	                           <div class="form-group_label">新密码确认</div>
	                           <input type="password" class="form-control confirm_new_pwd" name="confirm_password"  />
	                           <div class="error_msg confirm_password_error" style="display: none">密码不匹配</div>
	                        </div>
	                        <div class="btn_group">  
	                           <input type="submit" class="btn btn-primary btn-block" value="保存" />
	                        </div>
                     	</form>
                  	</div>
               	</div>
            </div>
            <div class="tab-content-item account-setting-panel setting-content-box @if($tab == 'transaction_password') current @endif" >
                <form class="alert-password-form" name="alert-tpwd-form" data-action="/api/account/changeTransactionPwd" onsubmit="return false">
						<div class="form-group">
					    	<div class="form-group-label">当前绑定手机号码</div>
					    	<input type="text" class="form-control reset_phone" readonly="readonly" value="{{ $user['phone'] }}" />
					    </div>
					    <div class="form-group">
				            <div style="padding-right: 110px;position: relative">
				                <input type="text" class="form-control" name="code" placeholder="验证码" />
				                <a href="javascript:void(0)" class="btn btn-success js-send-phonecode" style="position: absolute;right: 0px;top: 0px">获取验证码</a>
				            </div>
				            <div class="code_send_tip_info" style="display: none">
				                <span class="code_send_tip text-info"></span>
				                <span>如若未收到，请<span class="verificate_code_time">60</span>秒后再发送</span>
				            </div>
				        </div>
					    <div class="form-group">
					       <div class="form-group_label">新密码</div>
					       <input type="password" class="form-control" required="required" maxlength="50" name="password"  />
					    </div>
					    <div class="form-group">
					       <div class="form-group_label">新密码确认</div>
					       <input type="password" class="form-control confirm_new_pwd" name="confirm_password"  />
					       <div class="error_msg confirm_password_error" style="display: none">密码不匹配</div>
					    </div>
					    <div class="form-group">
					    	<span class="text-red">每月仅限修改3次，吾皇务必牢记保管好国库钥匙！</span>
					    </div>
					    <div class="btn_group">  
					       <input type="submit" class="btn btn-primary btn-block" value="提交" />
					    </div>
				</form>
			</div>
        </div>
    </div>
</div>
<form class="upload-form qr-upload-form" method="post" enctype="multipart/form-data">
    <input name="image" accept="image/*" type="file" class="upload-file qr-upload-file" />
</form>
@endsection
@section('footer')@endsection
@section('scripts')
<script src="{{ Helper::asset_url('/media/scripts/view/account/settings.js') }}"></script>
@endsection

