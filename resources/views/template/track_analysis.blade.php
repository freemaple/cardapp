<?php 
	$GA_User_ID = config('site.ga_account_id');
?>
@if(!empty($GA_User_ID))
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id={{ $GA_User_ID }}"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());
	  gtag('config', '{{ $GA_User_ID }}');
	</script>
@endif
