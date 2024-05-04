<?php
require_once __DIR__ . '/../../config.php';

if($_SERVER['REQUEST_METHOD'] == 'DELETE') {

    // Создаем экземпляр подключения к БД
    $str = 'mysql:host=' . CONFIG['DATABASE']['host'] . ';dbname=' . CONFIG['DATABASE']['dbname'];
    $db = new PDO($str, CONFIG['DATABASE']['login'], CONFIG['DATABASE']['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Выполняем запрос удаления
    $statement = $db->prepare('DELETE FROM type_service WHERE id = :id');
    $statement->execute(['id' => $_REQUEST['id']]);

    // Отправляем ответ
    http_response_code(200);
    echo 'Запись успешна удалена';
} else {
    http_response_code(405);
    echo 'Проищошла ошибка, обратитесь к администратору сервера';
}
?>