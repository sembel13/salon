<?php
$TITLE = 'Мастера';
require_once __DIR__ . '/../../template/header.php';

function getAll() {
    return sendSqlAndGetData("
        SELECT
         masters.id,
         CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio,
         masters.phone,
         DATE_FORMAT(masters.date_start_work, '%d.%m.%Y') AS human_date_start_work,
         DATE_FORMAT(masters.date_end_work, '%d.%m.%Y') AS human_date_end_work
        FROM masters 
    ");
}

$records = getAll();
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Мастера</h1>
            <a class="btn btn-success" href="create.php">
                <i class="fa-solid fa-circle-plus"></i>
                Добавить мастера
            </a>

            <table id="my-table" class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">ФИО</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">Дата устройства</th>
                    <th scope="col">Дата увольнения</th>
                    <th class="text-center" scope="col">Подробнее</th>
                    <th class="text-center" scope="col">Редактировать</th>
                    <th class="text-center" scope="col">Удалить</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $item): ?>
                    <tr>
                        <td><?=$item['master_fio']?></td>
                        <td><?=$item['phone']?></td>
                        <td><?=$item['human_date_start_work']?></td>
                        <td><?=$item['human_date_end_work']?></td>
                        <td class="text-center">
                            <a href="show.php?id=<?=$item['id']?>">
                                <i class="fa-solid fa-folder"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="edit.php?id=<?=$item['id']?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <i class="fa-sharp fa-solid fa-trash remove-element color-red" onclick="removeElement(this, <?=$item['id']?>, '/admin/masters/delete.php')"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
