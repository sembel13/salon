<?php
$TITLE = 'Просмотр заказа';
require_once __DIR__ . '/../../template/header.php';

$record = sendSqlAndGetData("
        SELECT
            records.*,
            CONCAT(records.cost_without_discount, ' ₽') AS cost_without_discount_label,
            CONCAT(records.cost, ' ₽') AS cost_label,
            services.name AS service_name,
            status_record.name AS status_record_name,
            DATE_FORMAT(records.date, '%d.%m.%Y') AS human_date,
            CONCAT(records.hour, ' часов') AS human_hour,
            CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio,
            CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio
        FROM records
        INNER JOIN services
            ON services.id = records.service_id
        INNER JOIN clients
            ON clients.id = records.client_id
        INNER JOIN masters
            ON masters.id = records.master_id
        INNER JOIN status_record
            ON status_record.id = records.status_record_id
        WHERE records.id = :id
    ", [
    'id' => $_REQUEST['id']
])[0];

if($record == null) {
    header('Location: /admin/records');
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h3>Просмотр записи</h3>

            <p>Статус услуги: <b><?=$record['status_record_name']?></b></p>
            <p>Наименование услуги: <b><?=$record['service_name']?></b></p>
            <p>ФИО мастера: <b><?=$record['master_fio']?></b></p>
            <p>ФИО клиента: <b><?=$record['client_fio']?></b></p>
            <p>Дата оказания услуги: <b><?=$record['human_date']?></b></p>
            <p>Время оказания услуги: <b><?=$record['human_hour']?></b></p>
            <p>Стоимость: <b><?=$record['cost_label']?> (без скидки: <?=$record['cost_without_discount_label']?>)</b></p>
        </div>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
