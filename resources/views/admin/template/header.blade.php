<nav class="navbar navbar-fixed-top navbar-inverse site_nav_top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ route('admin_home') }}">系统</a>
        </div>
        @if(!empty($user))
        <div class="nav_info">
            <ul class="nav navbar-nav">
                @if($user->is_admin == '1')
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        系统管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin_user') }}">用户管理</a></li>
                        <li><a href="{{ route('admin_role') }}">角色管理</a></li>
                    </ul>
                </li>
                @endif
                @if($user->is_admin == '1')
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        日志管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/logs" target="_blank">系统日志</a></li>
                    </ul>
                </li>
                @endif
                @if(array_intersect(['admin', 'card'], $admin_user['allRoles']))
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        名片/文章管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin_card') }}">名片列表</a></li>
                        <li style="display: none"><a href="">文章分类</a></li>
                        <li><a href="{{ route('admin_post') }}">文章</a></li>
                    </ul>
                </li>
                @endif
                @if(array_intersect(['admin', 'customer'], $admin_user['allRoles']))
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        会员管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin_customer') }}">会员信息</a></li>
                        <li><a href="{{ route('admin_customer_score') }}">业绩表现</a></li>
                        @if($user->is_admin == '1')
                        <li><a href="{{ route('admin_customer_level') }}">等级</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(array_intersect(['admin', 'payout'], $admin_user['allRoles']))
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        提现管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin_payout_apply') }}">提现管理</a></li>
                    </ul>
                </li>
                @endif
                @if(array_intersect(['admin', 'site'], $admin_user['allRoles']))
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        网站管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/doc">帮助文档</a></li>
                        <li><a href="/admin/banner">banner</a></li>
                        <li><a href="/admin/notice">通知公告</a></li>
                        <li><a href="/admin/feedback">反馈记录</a></li>
                        <li><a href="/admin/phonecode">验证码</a></li>
                        <li><a href="/admin/site/config">配置</a></li>
                        <li><a href="/admin/site/goldconfig">每天红利设置</a></li>
                    </ul>
                </li>
                <li class="dropdown" style="display: none">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        股权管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/equity">股权</a></li>
                        <li><a href="/admin/equity/record">股权记录</a></li>
                        @if($user->is_admin == '1')
                        <li><a href="/admin/equity/config">股权配置</a></li>
                        @endif
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        金麦管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/gold/index">金麦统计</a></li>
                        <li><a href="/admin/gold/day">红利发放记录</a></li>
                    </ul>
                </li>
                @endif
                @if(array_intersect(['admin', 'product'], $admin_user['allRoles']))
                <li class="dropdown pc-nav">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        产品管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/productcategory">产品分类</a></li>
                        <li><a href="/admin/product?is_self=1">自营产品管理</a></li>
                        <li><a href="/admin/activitycategory">活动分类</a></li>
                        <li><a href="/admin/product/gift">礼包产品</a></li>
                    </ul>
                </li>
                @endif
                @if(array_intersect(['admin', 'order'], $admin_user['allRoles']))
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        订单管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin_orders', ['is_self' => '1', 'order_status_code' => 'shipping']) }}">自营订单</a></li>
                    </ul>
                </li>
                @endif
                @if(array_intersect(['admin', 'store'], $admin_user['allRoles']))
                <li class="dropdown pc-nav">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        个人店铺管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/store?status=1&pa=approval">店铺认证审核</a></li>
                        <li><a href="/admin/store">店铺列表</a></li>
                        <li><a href="/admin/product?is_self=0">个人店铺产品</a></li>
                        <li><a href="/admin/orders?is_self=0">个人店铺订单</a></li>
                         <li><a href="/admin/product/shareApply">共享专区申请</a></li>
                    </ul>
                </li>
                @endif
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        充值管理<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('admin_order_recharge', ['status' => '2']) }}">充值记录</a></li>
                    </ul>
                </li>
                <li class="dropdown pc-nav">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        运营观察室<b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="/admin/statistics/index">统计信息</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav pull-right">
              <li class="active"><a href="">Welcome  {{ $user->username or '' }}</a></li>
              <li><a href="{{ route('admin_user_alertpwd') }}">修改密码</a></li>
              <li><a href="{{ route('admin_user_logout') }}">注销</a></li>
            </ul>
        </div>
        @endif
    </div>
</nav>