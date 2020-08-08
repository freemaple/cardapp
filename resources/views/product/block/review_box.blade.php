<div class="goods-description" style="padding-top: 5px;">
    @if(count($goods_detail['reviews']))
    <div class="goods-detail-box">
        <div class="header clearfix"><span class="title">评论 | {{ $goods_detail['reviews_total'] or '0' }}</span>
            @if($goods_detail['reviews_total'] > 2)
                <span class="view-more"><a href="{{ Helper::route('product_reviews', [$goods_detail['id']]) }}">更多</a></span>
            @endif
        </div>
        <div class="content">
            <ul>
                @foreach($goods_detail['reviews'] as $rkey => $r)
                <li class="goods-review-item">
                    <div class="info clearfix">
                        <span class="avatar">
                            <span class="avatar-img @if(empty($r['avatar'])) avatar-img-default @endif"><img src="{{ $r['avatar'] or '' }}" /></span>
                        </span>
                        <span class="username">{{ isset($r['fullname']) ? $r['fullname'] : '' }}</span>
                        <span class="time"><span>{{ isset($r['created_at']) ? $r['created_at'] : '' }}</span></span>
                    </div>
                    <div class="content">
                        {!! $r['review_text'] !!}
                    </div>
                    @if(isset($r['spec']) && $r['spec'] != '')
                    <div class="spec-item">
                        <span>{{ $r['spec']  }}</span>
                    </div>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
</div>