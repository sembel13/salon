<?php
/**
 * Файл заголовка всех страниц (данный файл подключается на всех досутпных страницах для клиента)
 */


/**
 * Проверка авторизации пользователя
 */
function checkLogin() {
    if(in_array($_SERVER['PHP_SELF'], ['/login.php', '/selectRole.php'])) {
        if($_COOKIE['role'] == ROLE_ADMIN) {
            header('Location: /admin/records');
        }

        if($_COOKIE['role'] == ROLE_DIRECTOR) {
            header('Location: /director/reports');
        }
        return;
    }

    if(!in_array($_COOKIE['role'], [ROLE_DIRECTOR, ROLE_ADMIN])) {
        header('Location: /login.php');
    }

    $routeMainPage = explode('/', $_SERVER['PHP_SELF'])[1];
    if($_COOKIE['role'] == ROLE_ADMIN && $routeMainPage !== 'admin') {
        header('Location: /logout.php');
    } else if($_COOKIE['role'] == ROLE_DIRECTOR && $routeMainPage !== 'director') {
        header('Location: /logout.php');
    }
}

/**
 * Проверяет выбрал ли пользователь роль
 */
function isSelectedRole() {
    return isset($_COOKIE['role']);
}

/**
 * Проверяет выбрана ли текущая роль администратора
 */
function isCurrentRoleAdmin() {
    return $_COOKIE['role'] === ROLE_ADMIN;
}

/**
 * Проверяет выбрана ли текущая роль директора
 */
function isCurrentRoleDirector() {
    return $_COOKIE['role'] === ROLE_DIRECTOR;
}

// Объявляем глобальные переменные
const ROLE_DIRECTOR = 'DIRECTOR';
const ROLE_ADMIN = 'ADMINISTRATOR';

// Титульник текущей страницы
if(!isset($TITLE)) {
    $TITLE = 'Главная страница';
}

// Отключаем кеширование
header('Cache-Control: no-cache');

// Проверка авторизации
checkLogin();

// Подлкючаем конфигурационный файл
require_once __DIR__ . '/../config.php';

// Создаем экземпляр подключения к БД
$str = 'mysql:host=' . CONFIG['DATABASE']['host'] . ';dbname=' . CONFIG['DATABASE']['dbname'];
$db = new PDO($str, CONFIG['DATABASE']['login'], CONFIG['DATABASE']['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * Функция позволяющая получить данные из таблицы (-иц)
 * @param $sql - SQL код
 * @param $inputData - Входные данный
 * @return array|false - Выходные данные
 */
function sendSqlAndGetData($sql, $inputData = null) {
    global $db;

    $statement = $db->prepare($sql);
    $statement->execute($inputData);
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Функция позволяющая вставить данные в таблицу
 * @param $sql - SQL код
 * @param $inputData - Входные данный
 * @return int|bool - Получает ID вставленной таблицы или ЛОЖЬ если запрос не удачно завершился
 */
function sendSql($sql, $inputData = null) {
    global $db;

    $statement = $db->prepare($sql);
    if($statement->execute($inputData)) {
        return $db->lastInsertId();
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/public/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />

    <script src="/public/js/popper.min.js"></script>
    <script src="/public/js/bootstrap.min.js"></script>
    <script src="/public/js/jquery.min.js"></script>

    <title><?=$TITLE?></title>
</head>
<body>

<?php if(isSelectedRole()): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <?php if(isCurrentRoleAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/records">Заказы</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Услуги
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="/admin/type-services">Виды услуг</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/admin/services">Список услуг</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/admin/clients">Клиенты</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/admin/masters">Мастера</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/admin/reports">Отчеты</a>
                    </li>
                <?php endif; ?>

                <?php if(isCurrentRoleDirector()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/director/reports">Отчеты</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/logout.php">Выйти из системы</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>
