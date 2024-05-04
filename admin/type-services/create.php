<?php
$TITLE = 'Добавление вида услуги';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = sendSql('INSERT INTO type_service (name) VALUES (:name)', [
        'name' => $_REQUEST['name']
    ]);

    header('Location: /admin/type-services');
}
?>

<div class="container">
    <div class="row">
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Наименование</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Наименование" required>
            </div>

            <button type="submit" class="btn btn-primary">Создать запись</button>
        </form>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>


