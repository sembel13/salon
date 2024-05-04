<?php
$TITLE = 'Клиенты';
require_once __DIR__ . '/../../template/header.php';

function getAll() {
    return sendSqlAndGetData("
        SELECT
         clients.id,
         CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio,
         clients.phone,
         clients.email,
         bonus_card.name AS bonus_card_name
        FROM clients
        LEFT JOIN bonus_card
            ON bonus_card.id = clients.bonus_card_id 
    ");
}

$records = getAll();
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Клиенты</h1>
            <a class="btn btn-success" href="create.php">
                <i class="fa-solid fa-circle-plus"></i>
                Добавить клиента
            </a>

            <table id="my-table" class="table table-bordered">
                <thead>
                <tr>
                    <th scope="col">ФИО</th>
                    <th scope="col">Телефон</th>
                    <th scope="col">E-mail</th>
                    <th scope="col">Бонусная карта</th>
                    <th class="text-center" scope="col">Подробнее</th>
                    <th class="text-center" scope="col">Редактировать</th>
                    <th class="text-center" scope="col">Удалить</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $item): ?>
                    <tr>
                        <td><?=$item['client_fio']?></td>
                        <td><?=$item['phone']?></td>
                        <td><?=$item['email']?></td>
                        <td><?=$item['bonus_card_name']?></td>
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
                            <i class="fa-sharp fa-solid fa-trash remove-element color-red" onclick="removeElement(this, <?=$item['id']?>, '/admin/clients/delete.php')"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
