<link href="{{ asset('/media/theme/card/simple.css') }}" rel="stylesheet">
<style type="text/css">
	.triangle-down {
		width: 0;
		height: 0;
		display: block;
		position: absolute;
		top: 50%;
		margin-top: -43px;
		right: 0px;
		color: #ff9800
	}
	.triangle-down a {
		background-color: #ff9800
	}
	.triangle-down {
		width: 0;
		height: 0;
		display: block;
		position: absolute;
		top: 50%;
		margin-top: -43px;
		right: 0px;
		color: #ff9800
	}
	.slider-item {
		width: 100%;
		background-color: #ffffff
	}
	.slider-item img {
		display: block;
		height: 300px;
		width: auto;
		max-width: 100%;
		margin: auto;
	}
</style>
<div class="card-box">
	<div class="companyName-theme">{{ $card['card_info']['organization'] or '' }} </div> 
	<div style="background-size: 100%;background-image: url({{ HelperImage::storagePath($card['background_image']) }})">
		<header class="theme-hd">
			<div class="musicBtn">
				<div class="music ic-music-auto">
					<span class="iconfont icon-music"></span>
				</div>
			</div>
		</header>
		<div class="theme-info-pub">
			<div class="theme-infoProfile">
				<div class="pub-faceImg j-portrait js-avatar-edit">
					<img src="{{ HelperImage::getavatar($user['avatar']) }}"> 
					<span><i class="ic-pubfont ic-org ic-level-1"></i></span>
				</div>
				<h1  class="name text-elli j-userName">
					<div class="userName-theme">
						{{ $user['fullname'] }}
					</div> 
					<div class="department-text">
						{{ $card['card_info']['department'] or '' }} {{ $card['card_info']['position'] or '' }}
					</div>
				</h1>
			</div>
			<span class="triangle-down">
			   	<a href="{{ Helper::route('account_card_edit', [$card['card_number']]) }}" class="box-edit-btn" style="color: #fff">编辑</a>
			</span>
		</div>
	</div>
	<div>
		<div class="">
			<div class="card-box-header clearfix" style="">
				相册记载
			</div>
			<div class="card-album-content slider js-site-slider" style="position: relative;">
			    <ul class="slider-img-wrap js-slider-img-wrap">
			    	@if(empty($card['card_album']) || count($card['card_album']) == 0)
			    		<li class="slider-item" style="position: relative;"><img class="banner-image" src="{{ HelperImage::storagePath('card_album/default1.jpg') }}" alt="{{ $b['alt'] or '' }}">
			        	</li>
			        	<li class="slider-item" style="position: relative;"><img class="banner-image" src="{{ HelperImage::storagePath('card_album/default2.jpg') }}" alt="{{ $b['alt'] or '' }}">
			        	</li>
			    	@else
			        @foreach($card['card_album'] as $bkey => $ca)
			        <li class="slider-item" style="position: relative;"><img class="banner-image" src="{{ HelperImage::storagePath($ca['image']) }}" alt="{{ $b['alt'] or '' }}">
			        </li>
			        @endforeach
			        @endif
			    </ul>
			    <span class="triangle-down">
			    	<a href="{{ Helper::route('account_card_album', [$card['id']]) }}" class="box-edit-btn" style="color: #fff">编辑相册</a>
			    </span>
			</div>
		</div>
		<div class="simple-microlink-box">
			<div class="card-box-header clearfix">微链接<a href="/account/microlink?cid={{ $card['id'] }}" class="box-edit-btn" style="color: #fff">编辑栏目</a></div>
			<div class="simple-microlink-list">
				<ul class=" js-card-microlink-list">
			        @foreach($card['card_microlinks'] as $ikey => $microlink)
					<li class="simple-microlink-item js-edit-microlink" data-id="{{ $microlink['id'] }}" data-name="{{ $microlink['name'] }}" data-icon-id="{{ $microlink['icon_id'] }}">
						<div class="icon">
							{!! $microlink['svg'] !!}
						</div>
						<div class="name">{{ $microlink['name'] }}</div>
					</li>
			        @endforeach
				</ul>
			</div>
		</div>
	</div>
</div>
