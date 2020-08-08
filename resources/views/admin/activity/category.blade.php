@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
    <li><a href="/admin/productcategory">产品管理</a></li>
    <li class="active">产品分类</li>
</ul>
<div class="tree_panel clearfix">
   <div class="panel panel-info">
        <div class="panel-body">
            <div class="well">
                <form class="form-inline" role="form">
                    <div class="form-group">
                        <label class="control-label" for="name">分类名称</label>
                        <input type="text" class="form-control" name="name" value="{{ isset($form['name']) ? $form['name'] : ''  }}" placeholder="请输入名称">
                    </div>
                    <input type="hidden" name="pid" value="{{ isset($form['pid']) ? $form['pid'] : '' }}">
                    <button type="submit" class="btn btn-info">查询</button>
                </form>
            </div>
            <table class="table table-condensed table-striped">
                <thead>
                    <tr>
                        <th>编号</th>
                        <th>名称</th>
                        <th>描述</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody class="productcategory_table_list">
                   @foreach($activitycategorys as $key => $list)
                        <tr class="level">
                            <td>{{ $list['id'] }}</td>
                            <td>{{ $list['name'] }}</td>
                            <td>{{ $list['description'] }}</td>
                            <td>{{ $list['created_at'] }}</td>
                            <td>
                                <a href="/admin/activitycategory/product/{{ $list['id'] }}">产品</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@endsection