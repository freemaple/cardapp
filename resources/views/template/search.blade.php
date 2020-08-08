<script type="text/template" class="site-search-panel">
<div class="site-search-box clearfix">
	<div class="site-search-block">
        <form action="/search">
        	<span class="search-icon iconfont icon-input-search"></span>
        	<input type="input" class="form-control" name="keyword" value="{{ $search_keyword or '' }}" autofocus="autofocus"  placeholder="@lang('view.search.tips')" maxlength="300">
        </form>
		<a href="javascript:void(0)" class="close js-close-layer">@lang('view.words.cancel')</a>
    </div>
    <div class="trending-search" style="display: none">
    	<h2>@lang('view.search.hot')</h2>
    	<ul></ul>
    </div>
    <div class="search-history" style="display: none">
    	<h2>@lang('view.search.history')</h2>
    	<ul class="search-history-list"></ul>
    	<div class="clear-history-box">
    		<a class="js-clear-history" href="javascript:void(0)">@lang('view.search.history_clear')</a>
    	</div>
    </div>
</div>
</script>