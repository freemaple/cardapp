<script type="text/javascript">
    var share_data = {
        title: "{{ isset($share_data['title']) ? $share_data['title'] : ( isset($title) ? $title : '') }}",
        content: "{{ isset($share_data['content']) ? $share_data['content'] : '' }}",
        url: "{!! isset($share_data['url']) ? $share_data['url'] : url()->full() !!}",
        image: "{{ isset($share_data['image']) ? $share_data['image'] : '' }}"
    };
    if(share_data['title'] == ''){
        share_data['title'] = document.title;
    }
    if(share_data['content'] == ''){
        share_data['content'] = '人人有赏个人网页...让创业更简单';
    }
    if(share_data['image'] == ''){
        share_data['image'] = "{{ asset('/apple-touch-icon.png') }}";
    }
</script>
@if(!Helper::isWeixin())
<link href="{{ Helper::asset_url('/media/share/css/share.min.css') }}" rel="stylesheet">
<link href="{{ Helper::asset_url('/media/share/css/nativeShare.css') }}" rel="stylesheet">
<script src="{{ Helper::asset_url('/media/share/nativeShare.js') }}"></script>
<script type="text/javascript">
    var $config = {
        title               : share_data['title'] ? share_data['title'] : document.title,
        link               : share_data['url'],
        description         : share_data['content'],
        image               :  share_data['image'],
        wechatQrcodeTitle   : "微信扫一扫：分享", // 微信二维码提示文字
        wechatQrcodeHelper  : '<p>微信里点“发现”，扫一下</p><p>二维码便可将本文分享至朋友圈!</p>',
        sites: ['qzone', 'qq', 'weibo', 'wechat']
    };
    var nativeShare = new NativeShare()
    var shareData = {
        title: $config['title'],
        desc: $config['description'],
        // 如果是微信该link的域名必须要在微信后台配置的安全域名之内的。
        link: $config['link'],
        icon: $config['image']
    }
    nativeShare.setShareData(shareData);
    function share(command) {
        try {
            nativeShare.call(command)
        } catch (err) {
           
        }
    }
</script>
@endif
<script type="text/template" id="share-box-template">
    <div class="qr-box pop-bt-codebox" style="padding: 20px;width: 100%">
        <div>
            <span id="social-share"></span>
            <span id="nativeShare">
                <span class="list">
                   <span><a onclick="share('qZone')" class="qzone"><i class="i"></i></span>
                   <span><a class="js-copy-link" href="javascript:void(0)"><span class="iconfont icon-lianjie" style="font-size: 36px;color: #00f"></span></a></span>
               </span>
               
            </span>
        </div>
    </div>
</script>
<script type="text/javascript">
    var u = navigator.appVersion;
    var uc = u.split('UCBrowser/').length > 1  ? 1 : 0;
    var qq = u.split('MQQBrowser/').length > 1 ? 2 : 0;
    var wx = ((u.match(/MicroMessenger/i)) && (u.match(/MicroMessenger/i).toString().toLowerCase() == 'micromessenger'));
</script>