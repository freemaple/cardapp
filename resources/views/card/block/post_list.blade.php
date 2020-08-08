@foreach($posts as $ckey => $post)
<li class="list-item post-item js-post-item js-post-item-{{ $post['id'] }} clearfix">
    <div class="list-item-content clearfix">
        <a href="{{ Helper::route('post_view', [$post['post_number'], 'uid' => !empty($card_user) ? $card_user['u_id'] : '']) }}">
            <div class="img">
                <img data-img="{{ HelperImage::storagePath($post['image']) }}" class="lazyload" />
            </div>
            <div class="info">
                <div class="info-box">
                    <div class="name">{{ $post['name'] }}</div>
                </div>
            </div>
        </a>
    </div>
</li>
@endforeach