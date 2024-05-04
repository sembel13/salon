<?php
// Подлкючаем конфигурационный файл
require_once __DIR__ . '/../../config.php';

// Создаем экземпляр подключения к БД
$str = 'mysql:host=' . CONFIG['DATABASE']['host'] . ';dbname=' . CONFIG['DATABASE']['dbname'];
$db = new PDO($str, CONFIG['DATABASE']['login'], CONFIG['DATABASE']['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(isset($_REQUEST['client_id']) && isset($_REQUEST['service_id'])) {
    $statement = $db->prepare("
        SELECT
            CASE
              WHEN bonus_card.discount_percent IS NOT NULL THEN services.price - ((services.price / 100) * bonus_card.discount_percent)
              ELSE services.price
            END AS cost,
            services.price AS cost_without_discount
        FROM services
        INNER JOIN clients
            ON clients.id = :client_id
        LEFT JOIN bonus_card
            ON bonus_card.id = clients.bonus_card_id
        WHERE services.id = :service_id
    ");
    $statement->execute([
        'client_id' => $_REQUEST['client_id'],
        'service_id' => $_REQUEST['service_id']
    ]);
    echo json_encode($statement->fetchAll(PDO::FETCH_ASSOC)[0]);
} else {
    http_response_code(405);
    echo 'Проищошла ошибка, обратитесь к администратору сервера';
}
?>