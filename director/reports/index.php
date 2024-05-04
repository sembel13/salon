<?php
$TITLE = 'Отчеты администратора';
require_once __DIR__ . '/../../template/header.php';

$arrMasters = sendSqlAndGetData("SELECT id, CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio FROM masters");

?>

<div class="container">
    <div class="row">
        <h1>Отчеты</h1>
        <div class="col-12">
            <h4>Отчет об оказании услуг за период</h4>
            <form action="report-service-date.php" method="post">
                <div class="inline-field">
                    <div class="half">
                        <label for="date_start" class="form-label">Дата начала</label>
                        <input type="date" class="form-control" id="date_start" name="date_start" placeholder="Дата начала" required>
                    </div>

                    <div class="half">
                        <label for="date_end" class="form-label">Дата окончания</label>
                        <input type="date" class="form-control" id="date_end" name="date_end" placeholder="Дата окончания" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Получить отчет</button>
            </form>
        </div>

        <div class="col-12">
            <h4>Отчет об объеме работ мастеров</h4>
            <form action="report-volume-master.php" method="post">
                <button type="submit" class="btn btn-primary">Получить отчет</button>
            </form>
        </div>

        <div class="col-12">
            <h4>Отчет об объемах оказанных услуг</h4>
            <form action="report-volume-date.php" method="post">
                <div class="inline-field">
                    <div class="half">
                        <label for="date_start" class="form-label">Дата начала</label>
                        <input type="date" class="form-control" id="date_start" name="date_start" placeholder="Дата начала" required>
                    </div>

                    <div class="half">
                        <label for="date_end" class="form-label">Дата окончания</label>
                        <input type="date" class="form-control" id="date_end" name="date_end" placeholder="Дата окончания" required>
                    </div>
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
