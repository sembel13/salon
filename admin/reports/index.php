<?php
$TITLE = 'Отчеты администратора';
require_once __DIR__ . '/../../template/header.php';

$arrMasters = sendSqlAndGetData("SELECT id, CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio FROM masters");

?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Отчеты</h1>
            <h4>Отчет о занятости специалиста</h4>
            <form action="report-master-date.php" method="post">
                <div class="inline-field">
                    <div class="half">
                        <label for="master_id" class="form-label">Мастер</label>
                        <select class="form-select" name="master_id" id="master_id" aria-label="Мастер" required>
                            <option value="" selected>Выберите мастера</option>
                            <?php foreach ($arrMasters as $item): ?>
                                <option value="<?=$item['id']?>"><?=$item['master_fio']?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="half">
                        <label for="date" class="form-label">Дата</label>
                        <input type="date" class="form-control" id="date" name="date" placeholder="Дата" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Получить отчет</button>
            </form>
        </div>

        <div class="col-12">
            <h4>Отчет о сведеньях оказании услуг мастером</h4>
            <form action="report-master.php" method="post">
                <div class="mb-3">
                    <label for="master_id" class="form-label">Мастер</label>
                    <select class="form-select" name="master_id" id="master_id" aria-label="Мастер" required>
                        <option value="" selected>Выберите мастера</option>
                        <?php foreach ($arrMasters as $item): ?>
                            <option value="<?=$item['id']?>"><?=$item['master_fio']?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Получить отчет</button>
            </form>
        </div>
    </div>
</div>

<style>
    .col-12 {
        margin-top: 20px;
    }
</style>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
