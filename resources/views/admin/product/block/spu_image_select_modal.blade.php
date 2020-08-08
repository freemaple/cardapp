<div class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" id="select_product_sku_image_modal">
    <div class="modal-dialog" style="width: 1100px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    选择图片
                </h4>
            </div>
            <form class="form-horizontal save_product_spu_image" role="form" method="post" action="/admin/api/product/spu/saveimage" onsubmit="return false">
                <div class="modal-body">
                    {!! csrf_field() !!}
                    <input type="hidden" name="id" value="{{ $product['id'] }}">
                    <div class="img-ctr-body clear-fix">
                        <ul class="image_list">
                            @if(!empty($product['image']))
                            @foreach($product['image'] as $ikey => $image)
                            <li style="display: inline-block;vertical-align: middle;margin-right: 10px;margin-bottom: 10px" data-name="{{ $image['image'] }}">
                                <img src="{{ $image['imgsrc'] }}" width="100" style="cursor: default;" />
                                <p style="text-align: center;padding: 10px 0px"><input type="radio" name="image_check" value="{{ $image['image'] }}" /></p>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-info">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>