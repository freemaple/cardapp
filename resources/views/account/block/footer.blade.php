<div class="mobile-footer">
    <ul class="foot-nav-info clearfix">
        <li><a href="{{ Helper::route('home') }}"><span class="iconfont icon-home"></span><div><span class="text">首页</span></div></a></li>
        <li><a href="{{ Helper::route('account_center') }}"><span class="iconfont icon-idcard"></span><div><span class="text">我的应用</span></div></a></li>
        <li>
            <a href="{{ Helper::route('account_store') }}">
                <span class="iconfont icon-dianpu"></span>
                <div>
                    <span class="text" style="position: relative;">
                        <span>我的店铺</span>
                        <span class="store_order_number u-message_number" style="display:none;"></span> 
                    </span>
                </div>
            </a>
        </li>
        <li>
        	<a href="{{ Helper::route('account_message') }}">
        	    <span class="iconfont icon-message"></span>
        		<div>
        			<span class="text" style="position: relative;">消息
        				<span class="message_number u-message_number" style="display:none;"></span>
        			</span>
        		</div>
        	</a>
        </li>
        <li><a href="{{ Helper::route('account_index') }}"><span class="iconfont icon-user"></span><div><span class="text">账户</span></div></a></li>
    </ul>
</div>