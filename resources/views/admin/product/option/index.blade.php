@extends('admin.template.layout')
@section('content')
<ul class="breadcrumb">
    <li>产品管理</li>
    <li class="active">属性分类</li>
</ul>
<div class="well well-sm">
    <ul class="nav nav-pills">
        <li role="presentation" class="active"><a href="javascript:void(0)" class="btn btn-info j_modal_link"  data-modal-id="add_option_modal">
            添加
        </a></li>
    </ul>
</div>
<div class="panel panel-info">
    <div class="panel-body">
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th><input type="checkbox" class="rows_check" /> </th>
                <th>编号</th>
                <th>名称</th>
                <th>描述</th>
                <th>是否启用</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
                @if(isset($option_list))
                @foreach($option_list as $key => $list)
                    <tr>
                        <td><input type="checkbox" class="row_check" data-id="<?php echo $list['id'] ?>" /></td>
                        <td><?php echo $key+1 ?></td>
                        <td>
                            {{ $list['name'] }}
                        </td>
                        <td>
                            {{ $list['description'] }}
                        </td>
                        <td>{{ $list['enable'] == "1" ? "启用" : "禁用" }}</td>
                        <td>{{ $list['created_at'] }}</td>
                        <td>
                            <a class="btn btn-info update_item j_edit_modal_link"  data-load-action="/admin/api/option/load" data-id="{{ $list['id'] }}" data-modal-id="edit_option_modal">
                                编辑
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade"  tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true" id="add_option_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加属性分类
                </h4>
            </div>
            <form class="form-horizontal ajax_form" role="form" action="/admin/api/option/add" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">名称</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="input" name="name" required="required" maxlength="255" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="spec" class="col-sm-2 control-label">描述</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="input" name="description" required="required" maxlength="255" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">启用</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="enable">
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-info">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade"  tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true" id="edit_option_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    编辑属性分类
                </h4>
            </div>
            <form class="form-horizontal ajax_form" role="form" action="/admin/api/option/edit" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">名称</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="input" name="name" required="required" maxlength="255" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="spec" class="col-sm-2 control-label">描述</label>
                        <div class="col-sm-10">
                            <input class="form-control" type="input" name="description" required="required" maxlength="255" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">启用</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="enable">
                                <option value="1">是</option>
                                <option value="0">否</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-info">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection