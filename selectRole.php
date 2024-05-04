<?php
/**
 * Файл выбора пользователя
 * В зависимости от выбора пользователя, записывает в куки данные и редиректорит на нужную страницу
 */

require_once __DIR__ . '/template/header.php';

switch($_REQUEST['role']) {
    case ROLE_DIRECTOR:
            setcookie('role', ROLE_DIRECTOR);
            header('Location: /director/reports');
        break;
    case ROLE_ADMIN:
            setcookie('role', ROLE_ADMIN);
            header('Location: /admin/records');
        break;
    default: header('Location: /login.php');
}
?>