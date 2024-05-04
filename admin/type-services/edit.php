<?php
$TITLE = 'Редактирования вида услуги';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = sendSql('UPDATE type_service SET name = :name WHERE id = :id', [
        'name' => $_REQUEST['name'],
        'id' => $_REQUEST['id']
    ]);

    header('Location: /admin/type-services');
} else {
    $record = sendSqlAndGetData('SELECT * FROM type_service WHERE id = :id', [
        'id' => $_REQUEST['id']
    ])[0];

    if($record == null) {
        header('Location: /admin/type-services');
    }
}
?>

<div class="container">
    <div class="row">
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Наименование</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Наименование" value="<?=$record['name']?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить запись</button>
        </form>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>


