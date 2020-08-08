<?php foreach($productcategory_list as $key => $list) :?>
    <tr class="level level_<?= $list['level'] ?>">
        <td><?= $list['id'] ?></td>
        <td style="text-align:left;<?php if($list['level'] == 0):?> padding-left: 10px;<?php else: ?> padding-left: <?= 15* $list['level'] ?>px <?php endif ?>"><?= $list['name'] ?></td>
        <td><?= $list['description'] ?></td>
        <td><?= $list['created_at'] ?></td>
        <td>
            <a type="button" class="btn btn-info update_productcategory" data-id="<?= $list['id'] ?>" data-action="/admin/productcategory/load" href="javascript:void(0)">
                修改
            </a>
        </td>
    </tr>
<?php endforeach ?>