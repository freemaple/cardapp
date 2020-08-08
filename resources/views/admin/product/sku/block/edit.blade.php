<div class="modal fade"  tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true" id="edit_product_sku_modal">
    <div class="modal-dialog" style="width: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    修改sku{{ $product_sku['sku'] }}
                </h4>
            </div>
            <form class="form-horizontal" role="form" action="/admin/api/product/sku/edit" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $product_sku['id'] }}">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="price" class="col-sm-2 control-label">价格($)</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="price" value="{{ $product_sku['price'] or ''}}" placeholder="请输入价格($)">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="price" class="col-sm-2 control-label">市场价(元）</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="market_price" value="{{ $product_sku['market_price'] or ''}}" placeholder="请输入采购价">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="price" class="col-sm-2 control-label">共享积分</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="share_integral" value="{{ $product_sku['share_integral'] or ''}}" placeholder="请输入共享积分">
                        </div>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label for="price" class="col-sm-2 control-label">运费</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="shipping" value="{{ $product_sku['shipping'] or ''}}" placeholder="请输入运费">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="stock" class="col-sm-2 control-label">库存</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control"  name="stock" value="{{ $product_sku['stock'] or ''}}" placeholder="请输入库存">
                            </div>
                        </div>
                        <div class="col-sm-6 form-group">
                            <label for="is_sale" class="col-sm-2 control-label">是否上架</label>
                            <div class="col-sm-10">
                                <select id="is_sale" class="form-control" name="is_sale">
                                    <option value="1" @if($product_sku['is_sale'] == '1') selected="selected" @endif>是</option>
                                    <option value="0" @if($product_sku['is_sale'] != '1') selected="selected" @endif>否</option>
                                </select>
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