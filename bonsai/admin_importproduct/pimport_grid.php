<table class="table table-bordered">

<thead>
<tr>
<th>Mã phiếu</th>
<th>Ngày nhập</th>
<th>Tổng giá trị</th>
<th>Số lượng</th>
<th>Sản phẩm</th>
<th>Trạng thái</th>
<th>Thao tác</th>
<th>Hành động</th>
</tr>
</thead>

<tbody>

    <?php foreach($imports as $row): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['import_date'] ?></td>
        <td><?= number_format($row['total_value']) ?></td>
        <td><?= $row['quantity'] ?></td>
        <td><?= $row['product_name'] ?></td>
        <td>

            <?php
            if($row['status']=='pending'){
            echo "<span class='badge bg-warning'>Đang xử lý</span>";
            }
            elseif($row['status']=='completed'){
            echo "<span class='badge bg-success'>Đã hoàn thành</span>";
            }
            elseif($row['status']=='cancelled'){
            echo "<span class='badge bg-danger'>Đã hủy</span>";
            }
            ?>

        </td>
        <td>

            <a href="edit_import.php?id=<?=$row['id']?>" 
                class="btn btn-sm btn-primary">
                Chỉnh sửa
            </a>

        </td>
        <td>    

            <?php if($row['status']=="pending"){ ?>

                <a href="confirm_import.php?id=<?=$row['id']?>"
                class="btn btn-success btn-sm">
                Xác nhận
                </a>

                <a href="cancel_import.php?id=<?=$row['id']?>"
                class="btn btn-danger btn-sm">
                Hủy
                </a>

            <?php } ?>
            <?php if($row['status']=="completed"){ ?>

                <button class="btn btn-success btn-sm" disabled>
                Đã xác nhận
                </button>

            <?php } ?>
            <?php if($row['status']=="cancelled"){ ?>

                <button class="btn btn-danger btn-sm" disabled>
                    Đã hủy
                </button>

            <?php } ?>
            </td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>