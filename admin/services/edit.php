<?php
$TITLE = 'Редактирования услуги';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = sendSql('UPDATE services SET type_service_id = :type_service_id, name = :name WHERE id = :id', [
        'name' => $_REQUEST['name'],
        'type_service_id' => $_REQUEST['type_service_id'],
        'id' => $_REQUEST['id']
    ]);

    header('Location: /admin/services');
} else {
    $record = sendSqlAndGetData('SELECT * FROM services WHERE id = :id', [
        'id' => $_REQUEST['id']
    ])[0];

    $arrTypeServices = sendSqlAndGetData('SELECT * FROM type_service');

    if($record == null) {
        header('Location: /admin/services');
    }
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
                        <option value="<?=$item['id']?>" <?php echo $item['id'] == $record['type_service_id'] ? 'selected' : ''; ?> ><?=$item['name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Наименование</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Наименование" value="<?=$record['name']?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить запись</button>
        </form>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>


