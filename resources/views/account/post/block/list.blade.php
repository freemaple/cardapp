@foreach($posts as $ckey => $post)
<li class="list-item post-item js-post-item js-post-item-{{ $post['id'] }}" style="padding: 10px 0px 0px 10px;">
    <div class="list-item-header clearfix">
        <span class="list-no">文章编号: {{ $post['post_number'] }}</span>
        <span class="list-time-text">{{ $post['created_at'] }}</span>
    </div>
    <div class="list-item-content clearfix">
        <a href="@if(!empty($session_user) && $post['user_id'] == $session_user->id) {{ Helper::route('account_post_edit', $post['post_number']) }} 
        @elseif(!empty($post['post_reprint_id'])) {{ Helper::route('post_reprint_view', $post['post_reprint_id']) }} @else {{ Helper::route('post_view', [$post['post_number'], 'uid' => $session_user['u_id']]) }} 
        @endif">
            <div class="clearfix">
                <div class="img lazy" style="max-height: 120px;min-height: 60px;overflow: hidden;min-height: unset;">
                    <div style="max-height: 120px;overflow: hidden;"><img data-img="{{ $post['image'] }}" class="lazyload" style="height: 60px;width: auto;margin: auto" /></div>
                </div>
                <div class="info">
                    <div class="info-box" style="font-size: 0.24rem;">
                        <div class="name" style="max-height: 34px;overflow: hidden;">{{ $post['name'] }}</div>
                        <div class="info-block" style="margin-top: 2px;">
                            浏览数：<span class="value">{{ $post['view_number'] }}</span>
                        </div>
                        <div class="clearfix">
                             <div class="infobox-btn clearfix" style="padding: 10px 0px 5px 0px;">
                                @if(!empty($session_user) && $post['user_id'] == $session_user->id)
                                    <a href="javascript:void(0)" class="operate-btn js-post-delete" data-id="{{ $post['id'] }}">删除</a>
                                    @if(empty($post['post_reprint_id']))
                                    <a href="{{ Helper::route('account_post_edit', $post['post_number']) }}" class="operate-btn">编辑</a>
                                    @endif
                                @endif
                                @if(empty($post['post_reprint_id']))
                                <a href="{{ Helper::route('post_view', [$post['post_number'], 'uid' => $session_user['u_id']]) }}" class="operate-btn">浏览</a>
                                @else
                                <a href="javascript:void(0)" class="operate-btn js-post-reprint-delete" data-id="{{ $post['id'] }}">删除</a>
                                <a href="{{ Helper::route('post_reprint_view', $post['post_reprint_id']) }}" class="operate-btn">浏览</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</li>
@endforeach