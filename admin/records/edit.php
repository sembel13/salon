<?php
$TITLE = 'Редактирование заказа';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = sendSql('
        UPDATE records 
        SET status_record_id = :status_record_id
        WHERE id = :id
    ', [
        'id' => $_REQUEST['id'],
        'status_record_id' => $_REQUEST['status_record_id']
    ]);

    header('Location: /admin/records');
} else {
    $record = sendSqlAndGetData("
        SELECT
            records.*,
            CONCAT(records.cost_without_discount, ' ₽') AS cost_without_discount_label,
            CONCAT(records.cost, ' ₽') AS cost_label
        FROM records
        WHERE id = :id
    ", [
        'id' => $_REQUEST['id']
    ])[0];

    $arrStatusRecord = sendSqlAndGetData('SELECT * FROM status_record');
    $arrClients = sendSqlAndGetData("SELECT id, CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio FROM clients");
    $arrServices = sendSqlAndGetData("SELECT id, services.name FROM services");
    $arrMasters = sendSqlAndGetData("SELECT id, CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio FROM masters");

    if($record == null) {
        header('Location: /admin/records');
    }
}
?>

<div class="container">
    <div class="row">
        <form method="post">
            <div class="mb-3">
                <label for="status_record_id" class="form-label">Услуга</label>
                <select class="form-select" name="status_record_id" id="status_record_id" aria-label="Статус заказа" required>
                    <option value="">Выберите статус заказа</option>
                    <?php foreach ($arrStatusRecord as $item): ?>
                        <option value="<?=$item['id']?>" <?php echo $item['id'] == $record['status_record_id'] ? 'selected' : ''; ?> ><?=$item['name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="client_id" class="form-label">Клиент</label>
                <select class="form-select" name="client_id" id="client_id" aria-label="Клиент" required disabled>
                    <option value="" selected>Выберите клиента</option>
                    <?php foreach ($arrClients as $item): ?>
                        <option value="<?=$item['id']?>" <?php echo $item['id'] == $record['client_id'] ? 'selected' : ''; ?> ><?=$item['client_fio']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="service_id" class="form-label">Услуга</label>
                <select class="form-select" name="service_id" id="service_id" aria-label="Услуга" required disabled>
                    <option value="" selected>Выберите услугу</option>
                    <?php foreach ($arrServices as $item): ?>
                        <option value="<?=$item['id']?>" <?php echo $item['id'] == $record['service_id'] ? 'selected' : ''; ?> ><?=$item['name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="inline-field">
                <div class="half">
                    <label for="date" class="form-label">Дата оказания услуги</label>
                    <input type="date" class="form-control" id="date" name="date" placeholder="Дата оказания услуги" required disabled value="<?=$record['date']?>">
                </div>

                <div class="half">
                    <label for="hour" class="form-label">Час оказания услуги</label>
                    <select class="form-select" name="hour" id="hour" aria-label="Час оказания услуги" required disabled>
                        <option value="" selected>Выберите время</option>
                        <option value="9" <?php echo $record['hour'] == 9 ? 'selected' : ''; ?> >9 часов</option>
                        <option value="10" <?php echo $record['hour'] == 10 ? 'selected' : ''; ?> >10 часов</option>
                        <option value="11" <?php echo $record['hour'] == 11 ? 'selected' : ''; ?> >11 часов</option>
                        <option value="12" <?php echo $record['hour'] == 12 ? 'selected' : ''; ?> >12 часов</option>
                        <option value="13" <?php echo $record['hour'] == 13 ? 'selected' : ''; ?> >13 часов</option>
                        <option value="14" <?php echo $record['hour'] == 14 ? 'selected' : ''; ?> >14 часов</option>
                        <option value="15" <?php echo $record['hour'] == 15 ? 'selected' : ''; ?> >15 часов</option>
                        <option value="16" <?php echo $record['hour'] == 16 ? 'selected' : ''; ?> >16 часов</option>
                        <option value="17" <?php echo $record['hour'] == 17 ? 'selected' : ''; ?> >17 часов</option>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="select-master">
                <label for="master_id" class="form-label">Мастер</label>
                <select class="form-select" name="master_id" id="master_id" aria-label="Мастер" required disabled>
                    <option value="" selected>Выберите мастера</option>
                    <?php foreach ($arrMasters as $item): ?>
                        <option value="<?=$item['id']?>" <?php echo $item['id'] == $record['master_id'] ? 'selected' : ''; ?> ><?=$item['master_fio']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <h4>Итоговая стоимость услуги</h4>
            <div class="inline-field">
                <div class="half">
                    <label for="cost_without_discount_label" class="form-label">Стоимость (без скидки)</label>
                    <input type="text" class="form-control" id="cost_without_discount_label" name="cost_without_discount_label" placeholder="Выберите клиента и услугу" disabled value="<?=$record['cost_without_discount_label']?>">
                </div>
                <div class="half">
                    <label for="cost_label" class="form-label">Стоимость (с учетом скидки)</label>
                    <input type="text" class="form-control" id="cost_label" name="cost_label" placeholder="Выберите клиента и услугу" disabled value="<?=$record['cost_label']?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
