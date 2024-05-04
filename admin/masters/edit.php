<?php
$TITLE = 'Редактирование информации о мастере';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = sendSql("
        UPDATE masters
        SET
            surname = :surname,
            name = :name,
            patronymic = :patronymic,
            date_birthday = :date_birthday,
            gender = :gender,
            phone = :phone,
            date_start_work = :date_start_work,
            date_end_work = :date_end_work   
        WHERE id = :id
    ", [
        'id' => $_REQUEST['id'],
        'surname' => $_REQUEST['surname'],
        'name' => $_REQUEST['name'],
        'patronymic' => strlen($_REQUEST['patronymic']) > 0 ? $_REQUEST['patronymic'] : null,
        'date_birthday' => $_REQUEST['date_birthday'],
        'gender' => $_REQUEST['gender'],
        'phone' => $_REQUEST['phone'],
        'date_start_work' => $_REQUEST['date_start_work'],
        'date_end_work' => strlen($_REQUEST['date_end_work']) > 0 ? $_REQUEST['date_end_work'] : null,
    ]);

    // Очищаем список услуг предоставляемых мастером
    sendSql('DELETE FROM master_service WHERE master_id = :master_id', [ 'master_id' => $_REQUEST['id'] ]);

    // Добавляем услуги которые выполняет мастер
    if($_REQUEST['services'] && $_REQUEST['id']) {
        foreach ($_REQUEST['services'] as $service_id) {
            sendSql('INSERT INTO master_service (master_id, service_id) VALUES (:master_id, :service_id)', [
                'master_id' => $_REQUEST['id'],
                'service_id' => $service_id
            ]);
        }
    }

    header('Location: /admin/masters');
} else {
    $record = sendSqlAndGetData('SELECT * FROM masters WHERE id = :id', [
        'id' => $_REQUEST['id']
    ])[0];

    // Список услуг для выбора
    $arrServices = sendSqlAndGetData('SELECT name FROM services ORDER BY id ASC');
    $arrServices = array_map(function ($el) {
        return $el['name'];
    }, $arrServices);

    // Список услуг записанных на мастера
    $arrServiceMaster = sendSqlAndGetData('SELECT DISTINCT service_id FROM master_service WHERE master_id = :master_id', [ 'master_id' => $_REQUEST['id'] ]);
    $arrServiceMaster = array_map(function ($el) {
        return $el['service_id'];
    }, $arrServiceMaster);
}
?>

<div class="container">
    <div class="row">
        <form method="post">
            <div class="mb-3">
                <label for="surname" class="form-label">Фамилия</label>
                <input type="text" class="form-control" id="surname" name="surname" placeholder="Фамилия" value="<?=$record['surname']?>" required>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Имя" value="<?=$record['name']?>" required>
            </div>

            <div class="mb-3">
                <label for="patronymic" class="form-label">Отчество</label>
                <input type="text" class="form-control" id="patronymic" name="patronymic" placeholder="Отчество" value="<?=$record['patronymic']?>">
            </div>

            <div class="inline-field">
                <div class="half">
                    <label for="date_birthday" class="form-label">Дата рождения</label>
                    <input type="date" class="form-control" id="date_birthday" name="date_birthday" placeholder="Дата рождения" value="<?=$record['date_birthday']?>" required>
                </div>

                <div class="half">
                    <label for="gender" class="form-label">Пол</label>
                    <select class="form-select" name="gender" id="gender" aria-label="Пол" required>
                        <option value="">Выберите пол</option>
                        <option value="0" <?php echo $record['gender'] == 0 ? 'selected' : ''; ?> >Женский</option>
                        <option value="1" <?php echo $record['gender'] == 1 ? 'selected' : ''; ?> >Мужской</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Телефон" value="<?=$record['phone']?>" required>
            </div>

            <div class="inline-field">
                <div class="half">
                    <label for="date_start_work" class="form-label">Дата приема на работу</label>
                    <input type="date" class="form-control" id="date_start_work" name="date_start_work" placeholder="Дата приема на работу" value="<?=$record['date_start_work']?>" required>
                </div>

                <div class="half">
                    <label for="date_end_work" class="form-label">Дата увольнения</label>
                    <input type="date" class="form-control" id="date_end_work" name="date_end_work" placeholder="Дата увольнения">
                </div>
            </div>

            <div class="multifield">
                <p>Предоставляемые услуги</p>
                <a class="btn btn-success">Добавить услугу</a>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        const specialityField = dynamicField.initField($('.multifield'), 'services', 'Услуга', false, 'select', <?=json_encode($arrServices)?>);
        specialityField(<?=json_encode($arrServiceMaster)?>)
    })
</script>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>