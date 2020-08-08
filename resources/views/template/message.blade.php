@if(!empty(session('message')))
    <div class="site_msg_art msg_alert error show" style="position: fixed;top:100px">
        <a href="javascript:void(0)" class="msg_alert_close">×</a>
        <div class="msg_alert_content">{!! session('message') !!}</div>
    </div>
@endif