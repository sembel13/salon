<?php
$TITLE = 'Заказы';
require_once __DIR__ . '/../../template/header.php';

function getAll() {
    return sendSqlAndGetData("
        SELECT
            records.id,
            services.name AS service_name,
            CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio,
            CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio,
            DATE_FORMAT(records.date, '%d.%m.%Y') AS human_date,
            CONCAT(records.hour, ' часов') AS human_hour,
            status_record.name AS status_record_name
        FROM records
        INNER JOIN services
            ON services.id = records.service_id 
        INNER JOIN masters
            ON masters.id = records.master_id
        INNER JOIN clients
            ON clients.id = records.client_id
        INNER JOIN status_record
            ON status_record.id = records.status_record_id
    ");
}

$records = getAll();
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Заказы</h1>
            <a class="btn btn-success" href="create.php">
                <i class="fa-solid fa-circle-plus"></i>
                Создать заказ
            </a>

            <table id="my-table" class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">Услуга</th>
                    <th scope="col">ФИО мастера</th>
                    <th scope="col">ФИО клиента</th>
                    <th scope="col">Дата</th>
                    <th scope="col">Время</th>
                    <th scope="col">Статус заказа</th>
                    <th class="text-center" scope="col">Подробнее</th>
                    <th class="text-center" scope="col">Редактировать</th>
                    <th class="text-center" scope="col">Удалить</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $item): ?>
                    <tr>
                        <td><?=$item['service_name']?></td>
                        <td><?=$item['master_fio']?></td>
                        <td><?=$item['client_fio']?></td>
                        <td><?=$item['human_date']?></td>
                        <td><?=$item['human_hour']?></td>
                        <td><?=$item['status_record_name']?></td>
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
                            <i class="fa-sharp fa-solid fa-trash remove-element color-red" onclick="removeElement(this, <?=$item['id']?>, '/admin/records/delete.php')"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
