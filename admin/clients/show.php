<?php
$TITLE = 'Просмотр клиента';
require_once __DIR__ . '/../../template/header.php';

$record = sendSqlAndGetData("
        SELECT
         clients.*,
         DATE_FORMAT(clients.date_birthday, '%d.%m.%Y') AS human_date_birthday,
         CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio,
         bonus_card.name AS bonus_card_name
        FROM clients
        INNER JOIN bonus_card
            ON bonus_card.id = clients.bonus_card_id
        WHERE clients.id = :id
    ", [
    'id' => $_REQUEST['id']
])[0];

if($record == null) {
    header('Location: /admin/clients');
}
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h3>Просмотр записи</h3>

            <p>ФИО: <b><?=$record['client_fio']?></b></p>
            <p>Дата рождения: <b><?=$record['human_date_birthday']?></b></p>
            <p>Пол: <b><?=$record['gender'] == 0 ? 'Женский' : 'Мужской'?></b></p>
            <p>Телефон: <b><?=$record['phone']?></b></p>
            <p>E-mail: <b><?=$record['email']?></b></p>
            <p>Бонусная карта: <b><?=$record['bonus_card_name']?></b></p>
        </div>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
