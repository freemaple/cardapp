@extends('layouts.app')

@section('styles')
<style type="text/css">
    .wrap-content {
        background-color: #fff
    }
    .header-box {
        background: #fe7589;
        color: #fff;
        padding: 10px;
        position: relative;
        position: fixed;
        width: 100%;
        z-index: 2
    }
    .notice-text {
        padding: 0px 5px;
    }
    .notice-text-content {
        position: absolute;
        width: 100%;
        top: 0px;
        color: #fff;
        padding: 10px 10px 10px 20px;
    }
    .post-slider-item {
        max-height: 300px;
        overflow-y: hidden;
        position: relative;
    }
    .img-name {
        position: absolute;
        bottom: 0px;
        background-color: rgba(0, 0, 0, 0.5);
        padding: 10px 0px;
        text-align: center;width: 100%;
        color: #fff
    }
    .xanimate {
        position: absolute;
        top: 0px;
        left: 20px;
        z-index: 1;
        padding-left: 20px;
        font-size: 12px;
        white-space: nowrap;
        animation:  wordsLoop 5s linear 0ms infinite normal;
    }

    @keyframes wordsLoop {
        0% {
            transform: translateX(0px);
            -webkit-transform: translateX(0px);
            
        }
        100% {
           transform: translateX(-100%);
            -webkit-transform: translateX(-100%);
        }
    }

    @-webkit-keyframes wordsLoop {
        0% {
            transform: translateX(0px);
            -webkit-transform: translateX(0px);
          
        }
        100% {
            transform: translateX(-100%);
            -webkit-transform: translateX(-100%);
        }
    }
    .cate-title .search {
        padding: 10px;
    }
</style>
@endsection

@section('header')
@if(!empty($notice['content']))
<div class="mobile-header">
    <div class="mobile-header-box clearfix">
        <div class="header-box">
            <div class="notice-text-box">
                <div class="notice-text-content xanimate">通告：{{ $notice['content'] }}</div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('content')

@if(!empty($recomBeautyPost))
<div class="">
    <div style="padding: 8px" class="clearfix">美文推荐  
        <span class="pull-right"><a href="{{ Helper::route('beauty_post') }}" class="u-link text-info">已投稿文集</a></span>
    </div>
    <div class="slider js-site-slider">
        <ul class="slider-img-wrap js-slider-img-wrap">
            @foreach($recomBeautyPost as $bkey => $b)
            <li class="slider-item post-slider-item">
                <a title="{{ $b['name'] or '' }}" href="{{ route('post_view', $b['post_number']) }}">
                    <img class="banner-image" src="{{ HelperImage::storagePath($b['image']) }}" alt="{{ $b['alt'] or '' }}">
                    <div class="img-name">{{ $b['name'] or '' }}</div>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
<div class="site-category-block">
    <div class="cate-title cate-search" style="position: fixed;background-color: #fff;z-index: 2;width: 100%;max-width: 640px">
        <div class="search">
            <div class="search-box">
                <form action="{{ Helper::route('search_post') }}">
                    <span class="mark">
                        <i class="iconfont icon-search"></i>
                    </span>
                    <input type="text" class="flex pub-input" name="keyword" placeholder="请输入文章名称搜索">
                </form>
            </div>
        </div>
    </div>
    <div class="clearfix" style="padding-top: 55px;">
        <div class="site-category-aside">
            <div class="site-category-side-box">
                <ul>
                    <li class="site-category-side-item js-category-side-item js-category-side-item-0  }}" data-id="0">
                        <span>全部</span>
                    </li>
                    @if(!empty($categorys))
                    @foreach($categorys as $ckey => $cate)
                        <li class="site-category-side-item js-category-side-item js-category-side-item-{{ $cate['id']  }}" data-id="{{ $cate['id'] }}">
                            <span>{{ $cate['name'] }}</span>
                        </li>
                    @endforeach
                    @endif
                </ul>
            </div>
        </div>
        <div class="site-category-list">
            <ul class="clearfix site-category-list-box site-category-list-0">
                @include('article.block.c_post', ['posts' => $posts, 'category_id' => 0])
            </ul>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    //基础加载
    require(['zepto', 'base', 'mylayer', 'lazyload'], function ($, md_base, mylayer, lazyload) {
        $(".js-category-side-item").first().addClass('current');
        $(".js-category-side-item").on("click", function(){
            $(".js-category-side-item").removeClass('current');
            var id = $(this).attr('data-id');
            $(this).addClass('current');
            var category_item = $('.site-category-list-' +  id);
            if(category_item.size() == 0){
                $.ajaxGet('/api/category/getPost', {'category_id': id}, function(result){
                    if(result.code == 'Success'){
                        $(".site-category-list-box").hide();
                        $(".site-category-list").append('<ul class="clearfix site-category-list-box site-category-list-' + id +'">' + result.view +'</ul>');
                        $.imgLazyLoad();
                    }
                });
            } else {
                $(".site-category-list-box").hide();
                category_item.show();
            }
        });    
    }); 
</script>
@endsection