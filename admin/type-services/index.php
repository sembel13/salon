<?php
$TITLE = 'Виды услуг';
require_once __DIR__ . '/../../template/header.php';

function getAll() {
    return sendSqlAndGetData('SELECT * FROM type_service');
}

$records = getAll();
?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Виды услуг</h1>
            <a class="btn btn-success" href="create.php">
                <i class="fa-solid fa-circle-plus"></i>
                Добавить вид услуги
            </a>

            <table id="my-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Наименование</th>
                        <th class="text-center" scope="col">Редактировать</th>
                        <th class="text-center" scope="col">Удалить</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($records as $item): ?>
                    <tr>
                        <td><?=$item['name']?></td>
                        <td class="text-center">
                            <a href="edit.php?id=<?=$item['id']?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <i class="fa-sharp fa-solid fa-trash remove-element color-red" onclick="removeElement(this, <?=$item['id']?>, '/admin/type-services/delete.php')"></i>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
