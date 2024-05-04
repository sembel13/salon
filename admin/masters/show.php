<?php
$TITLE = 'Просмотр мастера';
require_once __DIR__ . '/../../template/header.php';

$record = sendSqlAndGetData("
        SELECT
         masters.*,
         DATE_FORMAT(masters.date_birthday, '%d.%m.%Y') AS human_date_birthday,
         CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio,
         DATE_FORMAT(masters.date_start_work, '%d.%m.%Y') AS human_date_start_work,
         DATE_FORMAT(masters.date_end_work, '%d.%m.%Y') AS human_date_end_work
        FROM masters
        WHERE masters.id = :id
    ", [
    'id' => $_REQUEST['id']
])[0];

$arrNameServiceMaster = sendSqlAndGetData("
    SELECT DISTINCT
        services.name    
    FROM master_service
    INNER JOIN services
        ON services.id = master_service.service_id  
", [
    'id' => $_REQUEST['id']
]);

if($record == null) {
    header('Location: /admin/masters');
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h3>Просмотр записи</h3>

            <p>ФИО: <b><?=$record['master_fio']?></b></p>
            <p>Дата рождения: <b><?=$record['human_date_birthday']?></b></p>
            <p>Пол: <b><?=$record['gender'] == 0 ? 'Женский' : 'Мужской'?></b></p>
            <p>Телефон: <b><?=$record['phone']?></b></p>
            <p>Дата приема на работу: <b><?=$record['human_date_start_work']?></b></p>
            <p>Дата увольнения: <b><?=$record['human_date_end_work'] ? $record['human_date_end_work'] : 'Не уволен'?></b></p>
            <p>Список услуг предоставляемые мастером:</p>
            <?php foreach ($arrNameServiceMaster as $nameService): ?>
                <span><b>- <?=$nameService['name']?></b></span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
    span {
        display: block;
        margin-left: 10px;
    }
</style>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
