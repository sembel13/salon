<?php
$TITLE = 'Добавление услуги';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = sendSql('INSERT INTO services (type_service_id, name) VALUES (:type_service_id, :name)', [
        'type_service_id' => $_REQUEST['type_service_id'],
        'name' => $_REQUEST['name']
    ]);

    header('Location: /admin/services');
} else {
    $arrTypeServices = sendSqlAndGetData('SELECT * FROM type_service');
}
?>

<div class="container">
    <div class="row">
        <form method="post">
            <div class="mb-3">
                <label for="type_service_id" class="form-label">Вид услуги</label>
                <select class="form-select" name="type_service_id" id="type_service_id" aria-label="Вид услуги" required>
                    <option value="">Выберите тип услуги</option>
                    <?php foreach ($arrTypeServices as $item): ?>
                        <option value="<?=$item['id']?>"><?=$item['name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Наименование</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Наименование" required>
            </div>

            <button type="submit" class="btn btn-primary">Создать запись</button>
        </form>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>


