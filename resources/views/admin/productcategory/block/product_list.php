<?php if(!empty($product_list)): ?>
<?php foreach($product_list as $key => $list) :?>
    <tr>
        <td><?= $list['id'] ?></td>
        <td><?= $list['product_category_name'] ?></td>
        <td><?= $list['name'] ?></td>
        <td><?= $list['spec'] ?></td>
        <td><?= $list['model_number'] ?></td>
        <td><?= $list['brand'] ?></td>
        <td><?= $list['material'] ?></td>
        <td><?= $list['view_number'] ?></td>
        <td><?= $list['created_at'] ?></td>
        <td>
            <a type="button" class="btn btn-info update_product" data-id="<?= $list['id'] ?>" data-action="/admin/product/load" href="javascript:void(0)">
                修改
            </a>
            <a type="button" class="btn btn-danger remove_product" data-action="/admin/product/remove/<?= $list['id'] ?>" data-id="<?= $list['id'] ?>" href="javascript:void(0)">
            删除
            </a>
        </td>
    </tr>
<?php endforeach ?>
<?php endif ?>