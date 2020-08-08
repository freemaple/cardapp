@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
  	<li><a href="{{ route('admin_post') }}">文章管理</a></li>
    <li class="active">文章列表</li>
</ul>
<div class="well">
    <form class="form-inline" role="form">
        <div class="form-group">
            <label class="control-label" for="nickname">发布人：</label>
            <input type="text" class="form-control" name="fullname" value="{{ $form['fullname'] or '' }}" placeholder="请输入姓名">
        </div>
        <div class="form-group">
            <label class="control-label" for="nickname">文章名称：</label>
            <input type="text" class="form-control" name="name" value="{{ $form['name'] or '' }}" placeholder="请输入文章名称">
        </div>
        <div class="form-group">
            <label class="control-label">发布时间从</label>
            <input type="text" class="start_date form-control laydate-icon" name="start_date" value="{{ $form['start_date'] or '' }}"  />
        </div>
        <div class="form-group">
           <label class="control-label">至</label>
            <input type="text" class="end_date form-control laydate-icon" name="end_date" value="{{ $form['end_date'] or '' }}" />
        </div>
        <button type="submit" class="btn btn-info">查询</button>
    </form>
</div>
@if(!empty($pager))
<div class="clearfix pager_block">
    <div class="item_status pull-left">
        共 {{ $posts->total() }} 个文章 {{ $posts->firstItem() }}-{{ $posts->lastItem() }}
    </div>
    <div class="pull-right pager_box">{{ $pager }}</div>
</div>
@endif
<div class="panel panel-info">
    <div class="panel-body">
       <table class="table table-condensed table-striped table-hover" style="min-width: 500px">
            <thead>
            <tr>
                <th width="120">会员姓名/昵称</th>
                <th width="120">会员等级</th>
                <th>文章标题</th>
                <th>文章分类</th>
                <th>文章主图</th>
                <th>允许转载</th>
                <th>文库中显示</th>
                <th>浏览量</th>
                <th>发布时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if (!empty($posts))
                    @foreach($posts as $key=>$list)
                        <tr>
                            <td><a href="{{ route('admin_customer_info', $list['userinfo']['id']) }}" target="_blank">{{ $list['userinfo']->fullname ? $list['userinfo']->fullname : $list['userinfo']->nickname }} （{{ !empty($list['userinfo']->phone) ? $list['userinfo']->phone : '' }})</a></td>
                            <td>
                                {{ $level_status[$list['userinfo']->level_status] or '' }} 
                                <p>注册时间：{{ $list['userinfo']->created_at or '' }}</p>
                            </td>
                            <td title="{{ $list['name'] }}">{{ $list['name'] }}</td>
                            <td title="文章分类">{{ $list['category_name'] }}</td>
                            <td><a href="{{ $list['image'] }}" target="_blank"><img src="{{ $list['image'] }}" width="80" /></a></td>
                            <td title="允许转载">{{ $list->public == '1' ? '允许' : '不允许' }}</td>
                            <td title="文库中显示">
                                {{ $list->in_article == '1' ? '显示' : '不显示' }}
                                @if($list['public'] == '1')
                                    <a type="button" class="js_in_article"  href="javascript:void(0)" data-id="{{ $list['id'] }}">
                                        @if($list['in_article'] != '1') <span style="color: #f00">加入文库</span> @else 取消加入文库 @endif
                                    </a>
                                @endif
                            </td>
                            <td title="浏览量">
                                {{ $list->view_number  }}
                            </td>
                            <td>{{ $list->created_at }}</td>
                            <td>
                                <a href="{{ route('post_view', $list['post_number']) }}" target="_blank">查看</a>
                                <a href="javascript:void(0)" class="js_remove_post" target="_blank" data-id="{{ $list['id'] }}">删除</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>  
@if(!empty($pager))
<div class="panel-body">
    <div class="text-center">{{ $pager }}</div>
</div>
@endif
@endsection
@section('scripts')
<script type="text/javascript">
    $(".js_in_article").on('click', function(){
        var post_id = $(this).attr('data-id');
        $.post('/admin/post/in_article', {'post_id': post_id}, function(result){
            if(result.code == '200'){
                window.location.reload();
            } else if(result.message != ''){
                $.showMessage(result.message);
            }
        });
    });
    $(".js_remove_post").on('click', function(){
        var post_id = $(this).attr('data-id');
        $.showConfirm('确定删除文章？', function(){
            $.post('/admin/post/remove', {'post_id': post_id}, function(result){
                if(result.code == '200'){
                    window.location.reload();
                } else if(result.message != ''){
                    $.showMessage(result.message);
                }
            });
        });
    });
</script>
@endsection
