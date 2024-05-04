<?php
$TITLE = 'Добавление клиента';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id = sendSql("
        INSERT INTO clients (
            surname,
            name,
            patronymic,
            date_birthday,
            gender,
            phone,
            email,
            bonus_card_id 
        ) VALUES (
            :surname,
            :name,
            :patronymic,
            :date_birthday,
            :gender,
            :phone,
            :email,
            :bonus_card_id    
        )
    ", [
        'surname' => $_REQUEST['surname'],
        'name' => $_REQUEST['name'],
        'patronymic' => strlen($_REQUEST['patronymic']) > 0 ? $_REQUEST['patronymic'] : null,
        'date_birthday' => $_REQUEST['date_birthday'],
        'gender' => $_REQUEST['gender'],
        'phone' => $_REQUEST['phone'],
        'email' => $_REQUEST['email'],
        'bonus_card_id' => strlen($_REQUEST['bonus_card_id']) > 0 ? $_REQUEST['bonus_card_id'] : null,
    ]);

    header('Location: /admin/clients');
} else {
    $arrBonusCard = sendSqlAndGetData('SELECT * FROM bonus_card');
}
?>

<div class="container">
    <div class="row">
        <form method="post">
            <div class="mb-3">
                <label for="surname" class="form-label">Фамилия</label>
                <input type="text" class="form-control" id="surname" name="surname" placeholder="Фамилия" required>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Имя" required>
            </div>

            <div class="mb-3">
                <label for="patronymic" class="form-label">Отчество</label>
                <input type="text" class="form-control" id="patronymic" name="patronymic" placeholder="Отчество">
            </div>

            <div class="inline-field">
                <div class="half">
                    <label for="date_birthday" class="form-label">Дата рождения</label>
                    <input type="date" class="form-control" id="date_birthday" name="date_birthday" placeholder="Дата рождения" required>
                </div>

                <div class="half">
                    <label for="gender" class="form-label">Пол</label>
                    <select class="form-select" name="gender" id="gender" aria-label="Пол" required>
                        <option value="">Выберите пол</option>
                        <option value="0">Женский</option>
                        <option value="1">Мужской</option>
                    </select>
                </div>
            </div>


            <div class="inline-field">
                <div class="half">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Телефон" required>
                </div>

                <div class="half">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="bonus_card_id" class="form-label">Бонусная карта</label>
                <select class="form-select" name="bonus_card_id" id="bonus_card_id" aria-label="Бонусная карта">
                    <option value="">Выберите бонусную карту</option>
                    <?php foreach ($arrBonusCard as $item): ?>
                        <option value="<?=$item['id']?>"><?=$item['name']?> (Скидка: <?=$item['discount_percent']?>%)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Создать запись</button>
        </form>
    </div>
</div>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>


