@if(!empty($review_list))
@foreach($review_list as $rkey => $r)
<li class="goods-review-item">
    <div class="info clearfix">
        <span class="avatar">
            <span class="avatar-img"><img src="{{ $r['avatar'] or '' }}" /></span>
        </span>
        <span class="username">{{ $r['fullname'] or '' }}</span>
        <span class="time"><span>{{ $r['created_at'] or '' }}</span></span>
    </div>
    <div class="content">
        {!! $r['review_text'] !!}
    </div>
    @if(!empty($r['spec']))
    <div class="spec-item">
        <span>{{ $r['spec']  }}</span>
    </div>
    @endif
</li>
@endforeach
@endif