@extends('layouts.app')

@section('styles')
<style type="text/css">
    body {
        background: #f5f5f5;
    }
    
</style>
@endsection

@section('header')
    <div class="mobile-header clearfix">
        <div class="mobile-header-box clearfix">
            <div class="mobile-header-title">{{ $title }}</div>
        </div>
    </div>
@endsection

@section('content')
<div class="product-list-box">
        <ul class="clearfix product-list viewd_goods_list">
           
        </ul>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        //基础加载
        require(['zepto', 'base', 'mylayer', 'scrollComponent', 'lazyload'], function ($, md_base, Lazyload) {
            var ids = window.localStorage.getItem('viewd_goods_id');
            if(!ids){
                ids = [];
            } else {
                ids = JSON.parse(ids);
            }
            if(ids.length > 0){
                ids = ids.slice(0, 50);
                $.ajaxGet('/api/getViewdProducts', {
                    goods_ids: ids.join(','),
                    type: 'list'
                }, function(rst){
                    if(rst.view){
                        $(".viewd_goods_list").html(rst.view);
                        $.imgLazyLoad();
                    }
                })
            }
           
        }); 
    </script>
@endsection


@section('copyright', view('template.copyright'))
