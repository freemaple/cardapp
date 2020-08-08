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
@endif