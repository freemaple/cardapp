@if(!empty($posts))
@foreach($posts as $ckey => $post)
<li class="list-item js-post-item js-post-item-{{ $post['id'] }} clearfix">
    <a href="{{ Helper::route('post_view', [$post['post_number']]) }}">
        <div class="img">
            <img data-img="{{ HelperImage::storagePath( $post['image']) }}" class="lazyload" />
        </div>
        <div class="info">
            <div class="info-box">
                <div class="name">{{ $post['name'] }}</div>
            </div>
        </div>
    </a>
</li>
@endforeach
@if(count($posts)>=10)
<li class="clearfix" style="text-align: center;padding: 10px 0px">
    <a href="{{ Helper::route('article_category_view', [$category_id]) }}" class="u-link">查看更多</a>
</li>
@endif
@else
    <div class="no-results">
        <div class="result-img">
            <div class="result-img">@include('template.rote')</div>
        </div>
        <div class="result-content">
            此宝地尚未开采,需我王来坐拥江山！<br />
            <a href="{{ Helper::route('account_post_add') }}" class="u-link text-info">(请您马上登基)</a>
        </div>
    </div>
@endif