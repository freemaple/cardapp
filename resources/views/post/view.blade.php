@extends('layouts.app')
@section('meta')
<meta property="og:title" content="{{ $post['name'] or '' }}" />
<meta property="og:description" content="{!! $post['name'] or '' !!}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{ Helper::route('post_view', [$post['id']]) }}" />
<meta property="og:image" content="" />
<meta property="og:site_name" content="{{ config('app.name') }}" />
@endsection

@section('styles')
<link href="//vjs.zencdn.net/5.19/video-js.min.css" rel="stylesheet">
@endsection

@section('header')@endsection

@section('content')
@if(empty($post))
<div class="no_results">
    <p class="oops">哎呀! </p>
    <div class="result_content">
        @if(!empty($message))
            <p>{{ $message }}</p>
        @else
            <p>文章不存在</p>
        @endif
    </div>
     <div class="control_group">
        <a class="btn btn_success" href="{{ Helper::route('home') }}">浏览其它</a>
    </div>
</div>
@else
<div class="j_detail">
	<div class="j_detail_block clearfix">
		<div class="toptit">
			<h3>{{ $post['name'] }}</h3>
            @if(!empty($post['post_music']['url']))
			<div class="musicBtn play" data-play="1">
				<div class="music ic-music-auto">
					<span class="iconfont icon-music"></span>
				</div>
			</div>
            @endif
		</div>
		<div class="post-user-info">
			<div class="avatar">
				<div class="avatar-info">
		 			<a href="{{ Helper::route('card_view', [isset($card['card_number']) ? $card['card_number'] : '']) }}"><img src="{{ HelperImage::getavatar($user['avatar']) }}" /></a>
        		</div>
        		<div class="title">
                    {{ $user->fullname }} 
                    {{ $user->phone}}
        		</div>
        		<div class="rightbox">
		 			<a href="{{ Helper::route('article') }}"><span>文库</span></a>
        		</div>
			</div>
		</div>
		<div class="little-info">
			<span>发布于{{ $post['created_at'] }} </span> <span><loc-i18n>浏览量</loc-i18n><em>{{ $post['view_number'] }}</em></span>
            <a href="javascript:void(0)" class="js-t-play"><img src="{{ Helper::asset_url('/media/images/voice.gif') }}" width="20" style="    vertical-align: middle;display: inline-block;margin-right: 5px">语音播放</a>
		</div>
	</div>
	<div class="box-description" onselectstart="return false" oncontextmenu="return false;">
        <div class="description-block">
            <div style="position: relative;width: 100%;" class="s-img-box">
                <img src="{{ HelperImage::storagePath($post['image']) }}" /></div>
            </div>
            <div style="position: relative;width: 100%;" class="s-img-box">
                <div class="b_img">
                    <a href="{{ $ad_link }}"><img src="{{ $ad_image }}" style="background-color: #f5f5f5;width: 100%" /></a>
                    <a class="ad_link" href="{{ $ad_link }}">进入看看</a>
                </div>
            </div>
            @if(!empty($post['video']))
            <div style="padding-top: 5px;">
                <video
                height='200'
                id="my-player"
                class="video-js"
                controls
                preload="auto"
                poster="{{ Helper::asset_url('/media/images/poster.png') }}" 
                style="width: 100%;"
                data-setup='{}'>
                    <source src="{{ HelperImage::storagePath($post['video']) }}" type="video/mp4"></source>
                    <p class="vjs-no-js">
                        对不起，您的浏览器不支持播放
                    </p>
                </video>
            </div>
            @endif
            <div style="padding: 20px 0px" class="description-content">
                {!! $post['description'] !!}
            </div>
        </div>
    </div>
    <div>
        <div id="ad">
            <div class="sysDefault-ad1">
                <div class="sysDefault-ad1-info">
                    <a href="{{ Helper::route('card_view', [isset($card['card_number']) ? $card['card_number'] : '']) }}" class="faceImgbox">
                        <img src="{{ HelperImage::getavatar($user['avatar']) }}">
                    </a>
                    @if(!empty($user['weixin_qr']))
                    <div class="codeShowbox">
                        <div class="codeImgbox">
                            <div class="codeImg">
                                <img src="{{ HelperImage::storagePath($user['weixin_qr']) }}" >
                            </div>
                        </div>
                        <p class="codeImgbox-name mt-10" data-i18n="componentEdit.weChatQrCode">微信二维码</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div style="text-align: center;font-size: 12px;color: #babab6">
        文章内容如有侵权，请联系删除！
    </div>
    <div style="margin: 40px 0px 0px;text-align: center;">
        <img src="{{ Helper::asset_url('/media/images/service/weixin_qr.jpg') }}" width="80"  class="wx_qr_image" />
        <p>扫描并关注人人有赏公众号</p>
    </div>
</div>
<input type="hidden" id="post_id" value="{{ $post['id'] }}"  />
@endif
@endsection
@section('footer')
<div class="mobile-footer post-footer">
    <ul class="clearfix">
        <li style="width: 25%">
            <a href="{{ Helper::route('home') }}" class="btn btn-default">
                <span class="iconfont icon-home" style="color: #00f;padding: 0px 2px;font-size: 20px"></span>
                <span>
                    首页
                </span>
            </a>
        </li>
        <li style="width: 30%">
            <a href="javascript:void(0)" class="btn btn-default js-share-link">
                <span class="iconfont icon-share" style="color: #00f;padding: 0px 2px;font-size: 20px"></span>
                <span>
                    分享
                </span>
            </a>
        </li>
        @if(!empty($session_user) && $post['user_id'] == $session_user->id )
        <li style="width: 45%">
            <a href="{{ Helper::route('account_post_edit', $post['post_number']) }}" class="btn btn-default">
                <span class="iconfont icon-edit" style="color: #00f;padding: 0px 2px;font-size: 20px">编辑</span>
            </a>
        </li>
        @endif
        @if(!empty($reprint_post) && !empty($session_user) && $reprint_post['user_id'] == $session_user->id)
        <li style="width: 45%">
            <a href="{{ Helper::route('account_post_index', ['type' => '2']) }}" class="btn btn-default">
                <span style="color: #00f;padding: 0px 2px;font-size: 20px">返回文章</span>
            </a>
        </li>
        @else
            @if(empty($session_user) || $post['user_id'] != $session_user->id)
                @if($post['public'] == '1')
                <li style="width: 45%">
                    <a href="javascript:void(0)" class="btn btn-default js-reprint-post">
                        <span class="iconfont icon-edit" style="color: #00f;padding: 0px 5px 0px 2px;font-size: 20px"></span>创建成我的
                    </a>
                </li>
                @else
                <li style="width: 45%">
                    <a href="{{ Helper::route('account_post_index') }}" class="btn btn-default">
                        <span class="iconfont icon-edit" style="color: #00f;padding: 0px 5px 0px 2px;font-size: 20px"></span>我也要创建
                    </a>
                </li>
                @endif
            @endif
        @endif
    </ul>
</div>
<script type="text/template" id="share-box-template">
    <div class="qr-box pop-bt-codebox" style="padding: 20px;width: 100%">
        <div>
            <span id="social-share"></span>
            <span id="nativeShare">
                <span class="list">
                    <span><a onclick="share('qqFriend')" class="qq"><i class="i"></i></a></span>
                    <span><a onclick="share('qZone')" class="qzone"><i class="i"></i></span>
               </span>
            </span>
        </div>
    </div>
</script>
<div id="tt" style="display: none">{{ $post['tt'] }}</div>

<audio id="post-music-audio" src="{{ !empty($post['post_music']['url']) ? HelperImage::storagePath($post['post_music']['url']) : '' }}" loop="loop" controls="controls" hidden preload autoplay="autoplay"></audio>

<audio id="tt-audio" src="{{ $post['tt-src'] }}"  controls="controls" hidden preload></audio>
@endsection
@section('scripts')
@if(!Helper::isWeixin())
<script type="text/javascript">
    var $config = {
        title               : "{{ $post['name'] }}",
        description         : "{{ $post['name'] }}",
        image               :  "{{ $post['image'] }}",
        wechatQrcodeTitle   : "微信扫一扫：分享", // 微信二维码提示文字
        wechatQrcodeHelper  : '<p>微信里点“发现”，扫一下</p><p>二维码便可将本文分享至朋友圈!</p>',
        sites: ['qzone', 'qq', 'weibo', 'wechat']
    };
    var nativeShare = new NativeShare()
    var shareData = {
        title: $config['title'],
        desc: $config['title'],
        // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
        link: $config['title'],
        icon: $config['image']
    }
    nativeShare.setShareData(shareData);
    function share(command) {
        try {
            nativeShare.call(command)
        } catch (err) {
           
        }
    }
@endif
</script>
<script src="{{ Helper::asset_url('/media/scripts/view/post.js') }}"></script>
@endsection
