<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" id="add_product_modal">
    <div class="modal-dialog" style="width: 1100px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加产品
                </h4>
            </div>
            <form class="form-horizontal ajax_form" role="form" action="/admin/api/product/add" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <div class="panel panel-default">
                        <div class="panel-heading">基本信息</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">所属分类</label>
                                <div class="col-sm-10">
                                    <select name="pid" class="form-control">
                                        <option value="">请选择</option>
                                        @if(!empty($productCategory_select_list))
                                        @foreach ($productCategory_select_list as $key => $category)
                                        <option value="{{ $category['id'] }}">@if($category['level'] == 0)&nbsp;@else @for($i = 0; $i <= $category['level']; $i++) &nbsp; @endfor @endif {{$category['name'] }}</option>  
                                        @endforeach ?>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Spu</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="input" name="spu" required="required" maxlength="255" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">名称</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="input" name="name" required="required" maxlength="255" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">中文名称</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="input" name="cn_name" required="required" maxlength="255" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">描述</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" rows="6" name="description" required="required" maxlength="255"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="is_sale" class="col-sm-2 control-label">是否上架</label>
                                <div class="col-sm-10">
                                    <select id="is_sale" class="form-control" name="is_sale">
                                        <option value="1">是</option>
                                        <option value="0">否</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">描述图</div>
                        <div class="panel-body">
                             <ul class="nav nav-pills">
                                <li role="presentation"><a href="javascript:void(0)" class="j_upload_product_image">本地图片</a></li>
                                <li role="presentation"><a href="javascript:void(0)" class="j_modal_link" data-modal-id="upload_store_modal">网络图片</a></li>
                            </ul>
                            <div>
                                <div class="img-ctr-body clear-fix">
                                    <ul class="image_list"></ul>
                                    <div class="no_image_box">
                                        <img  src="https://www.dianxiaomi.com/static/img/kong.png" style="cursor: default;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">变种信息</div>
                        <div class="panel-body">
                            <div>
                                <div class="option_box well well-sm">
                                    <ul class="option_list">
                                        <li class="option_item row">
                                            <div class="col-sm-6">
                                                <label for="is_sale" class="col-sm-2 control-label">属性</label>
                                                <select class="form-control">
                                                    @foreach($option_list as $okey => $option)
                                                    <option value="{{ $option['id'] }}">{{ $option['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-6">
                                                <label for="is_sale" class="col-sm-2 control-label">属性值</label>
                                                <input class="form-control" type="text" size="10" />
                                                <ul class="option_value_list">
                                                    
                                                </ul>
                                            </div>
                                            <a href="javascript:" style="display: none" class="remove_option removeOption">remove_circle</a>
                                        </li>
                                    </ul>
                                    <div class="text-center">
                                        <button type="button" class="btn btn-info add_option">添加</button>
                                    </div>
                                </div>
                                <div class="many_sku_box">
                                    <div class="">
                                        <div class="well well-sm">
                                            <div class="form-group row">
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">SKU</label>
                                                    <input class="form-control" type="text"  />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">默认采购价</label>
                                                    <input class="form-control" type="text"  />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">重量(g)</label>
                                                    <input class="form-control" type="text"  />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">尺寸</label>
                                                    <div>
                                                        <input  type="input" name="length" placeholder="长" size="1" required="required" maxlength="255" />
                                                        <input  type="input" name="width" placeholder="宽" size="1" required="required" maxlength="255" />
                                                        <input  type="input" name="height" placeholder="高" size="1" required="required" maxlength="255" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">中文报关名</label>
                                                    <input class="form-control" type="text" />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">英文报关名</label>
                                                    <input class="form-control" type="text" />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">报关重量(g)</label>
                                                    <input class="form-control" type="text" />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">报关金额</label>
                                                    <input class="form-control" type="text"  />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">海关编码</label>
                                                    <input class="form-control" type="text"  />
                                                </div>
                                                <div class="col-sm-2">
                                                    <label for="is_sale" class="control-label">危险运输品</label>
                                                    <input class="form-control" type="text"  />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
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