<?php
// Подлкючаем конфигурационный файл
require_once __DIR__ . '/../../config.php';

// Создаем экземпляр подключения к БД
$str = 'mysql:host=' . CONFIG['DATABASE']['host'] . ';dbname=' . CONFIG['DATABASE']['dbname'];
$db = new PDO($str, CONFIG['DATABASE']['login'], CONFIG['DATABASE']['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_REQUEST['service_id']) && isset($_REQUEST['date']) && isset($_REQUEST['hour'])) {
    $statement = $db->prepare("
        SELECT DISTINCT
            masters.id,
            CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio
        FROM masters
        INNER JOIN master_service
        ON 
          master_service.service_id = :service_id
          AND master_service.master_id = masters.id
        LEFT JOIN records
        ON
          records.master_id = masters.id
          AND records.date = :date
          AND records.hour = :hour
          AND records.status_record_id = 1
        WHERE 
        records.id IS NULL
        AND :date >= masters.date_start_work
        AND (
            :date < masters.date_end_work
            OR masters.date_end_work IS NULL 
        )
    ");
    $statement->execute([
        'service_id' => $_REQUEST['service_id'],
        'date' => $_REQUEST['date'],
        'hour' => $_REQUEST['hour']
    ]);
    echo json_encode($statement->fetchAll(PDO::FETCH_ASSOC));
} else {
    http_response_code(405);
    echo 'Проищошла ошибка, обратитесь к администратору сервера';
}
?>