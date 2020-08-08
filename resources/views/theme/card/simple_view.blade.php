<link href="{{ Helper::asset_url('/media/theme/card/simple.css') }}" rel="stylesheet">
<div class="card-box">
	<div style="background-size: 100%;background-image: url({{ HelperImage::storagePath($card['background_image']) }})">
		<header class="theme-hd">
			<div class="companyName-theme">{{ $card['card_info']['organization'] or '' }} </div> 
			@if(!empty($card['card_music']['url']))
			<div class="musicBtn play" data-play="1">
				<div class="music ic-music-auto">
					<span class="iconfont icon-music"></span>
				</div>
			</div>
			@endif
		</header>
		<div class="theme-infoProfile" >
			<div  class="pub-faceImg j-portrait">
				<img src="{{ HelperImage::getavatar($user->avatar) }}"> 
			</div>
			<h1  class="name text-elli j-userName">
				<div class="userName-theme">
					<span style="color: #fff">{{ $user['fullname'] }}</span>
					<a href="tel:{{ $user['phone'] }}" class="pup-a-tel"><span class="iconfont icon-phone"></span></a>
					@if(!empty($user['weixin_qr']))
					<a href="javascript:void(0)" class="js-show-weixin"><span style="font-size: 16px;color: #23c768;border-radius: 5px;margin-left: 10px;border: 1px solid #1afa29;padding: 2px">加微信</span></a>
					@endif
				</div> 
				<div class="department-text">{{ $card['card_info']['department'] or '' }} {{ $card['card_info']['position'] or '' }}</div> 
			</h1>
		</div>
	</div>
	<div>
		<div style="background-color: #f5f5f5">
			<div style="padding: 10px;background-color: #ffffff;border-bottom: 1px solid #eeeeee" class="clearfix">风采相册</div>
			<div class="slider js-site-slider">
			    <ul class="slider-img-wrap js-slider-img-wrap">
			    	@if(empty($card['card_album']) || count($card['card_album']) == 0)
			    		<li class="slider-item" style="position: relative;max-height: 400px"><img class="banner-image" style="background-color: #eee" src="{{ HelperImage::storagePath('card_album/default1.jpg') }}" alt="{{ $b['alt'] or '' }}">
			        	</li>
			        	<li class="slider-item" style="position: relative;max-height: 400px"><img class="banner-image" src="{{ HelperImage::storagePath('card_album/default2.jpg') }}" alt="{{ $b['alt'] or '' }}" style="background-color: #eee">
			        	</li>
			    	@else
				        @foreach($card['card_album'] as $bkey => $ca)
				        <li class="slider-item"><img class="banner-image" src="{{ HelperImage::storagePath($ca['image']) }}" alt="{{ $b['alt'] or '' }}"></li>
				        @endforeach
			        @endif
			    </ul>
			</div>
		</div>
		<div style="position: relative;">
			<a href="{{ $ad_link}}">
				<div style="position: absolute;top: 0.24rem;left: 0.48rem;font-size: 30px;color: #fff;width: 5.48rem;text-align: center">{{ $ad_name }}</div>
				<img src="{{ Helper::asset_url('/media/images/store.gif') }}" style="width: 100%">
			</a>
			@if(!empty($session_user) && $session_user->id == $user->id && !$store_enable)
			<p style="padding: 10px 0px;font-size: 16px;text-align: center;background-color: #f5f5f5">拥有网店！就拥有事业！<a href="{{ Helper::route('account_store') }}" style="color: #f00;text-decoration: underline;">我要开店入驻</a></p>
			@endif
		</div>
		@if(!empty($microlinks) && count($microlinks) > 0)
		<div class="simple-microlink-box">
			<div style="padding: 10px;background-color: #ffffff;border-bottom: 1px solid #eeeeee" class="clearfix">微链接</div>
			<div class="simple-microlink-list">
				<ul class="js-card-microlink-list">
			        @foreach($microlinks as $ikey => $microlink)
					<li class="simple-microlink-item js-edit-microlink" data-id="{{ $microlink['id'] }}" data-name="{{ $microlink['name'] }}" data-icon-id="{{ $microlink['icon_id'] }}">
						<a href="{{ $microlink['link'] }}">
							<div class="icon">
								{!! $microlink['svg'] !!}
							</div>
							<div class="name">{{ $microlink['name'] }}</div>
						</a>
					</li>
			        @endforeach
				</ul>
			</div>
		</div>
		@endif
	</div>
</div>
<style type="text/css">
	@keyframes scaleDraw {  /*定义关键帧、scaleDrew是需要绑定到选择器的关键帧名称*/
        0%{
            transform: scale(1);  /*开始为原始大小*/
        }
        25%{
            transform: scale(1.1); /*放大1.1倍*/
        }
        50%{
            transform: scale(1);
        }
        75%{
            transform: scale(1.1);
        }
    }
    .ballon{
        -webkit-animation: scaleDraw 1s ease-in-out infinite;
     }

</style>