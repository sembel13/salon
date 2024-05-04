<?php
$TITLE = 'Создание заказа';
require_once __DIR__ . '/../../template/header.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    sendSql("
        INSERT INTO records (
            service_id,
            master_id,
            client_id,
            date,
            hour,
            status_record_id,
            cost,
            cost_without_discount
        ) VALUES (
            :service_id,
            :master_id,
            :client_id,
            :date,
            :hour,
            :status_record_id,
            :cost,
            :cost_without_discount
        )
    ", [
        'service_id' => $_REQUEST['service_id'],
        'master_id' => $_REQUEST['master_id'],
        'client_id' => $_REQUEST['client_id'],
        'date' => $_REQUEST['date'],
        'hour' => $_REQUEST['hour'],
        'status_record_id' => $_REQUEST['status_record_id'],
        'cost' => $_REQUEST['cost'],
        'cost_without_discount' => $_REQUEST['cost_without_discount']
    ]);

    header('Location: /admin/records');
} else {
    $arrClients = sendSqlAndGetData("SELECT id, CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio FROM clients");
    $arrServices = sendSqlAndGetData("SELECT id, price, services.name FROM services");
}
?>

<div class="container">
    <div class="row">
        <form method="post">
            <input type="hidden" name="status_record_id" value="1">
            <input type="hidden" name="cost">
            <input type="hidden" name="cost_without_discount">


            <div class="mb-3">
                <label for="client_id" class="form-label">Клиент</label>
                <select class="form-select" name="client_id" id="client_id" aria-label="Клиент" required>
                    <option value="" selected>Выберите клиента</option>
                    <?php foreach ($arrClients as $item): ?>
                        <option value="<?=$item['id']?>"><?=$item['client_fio']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="service_id" class="form-label">Услуга</label>
                <select class="form-select" name="service_id" id="service_id" aria-label="Услуга" required>
                    <option value="" selected>Выберите услугу</option>
                    <?php foreach ($arrServices as $item): ?>
                        <option value="<?=$item['id']?>"><?=$item['name']?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="inline-field">
                <div class="half">
                    <label for="date" class="form-label">Дата оказания услуги</label>
                    <input type="date" class="form-control" id="date" name="date" placeholder="Дата оказания услуги" required disabled>
                </div>

                <div class="half">
                    <label for="hour" class="form-label">Час оказания услуги</label>
                    <select class="form-select" name="hour" id="hour" aria-label="Час оказания услуги" required disabled>
                        <option value="" selected>Выберите время</option>
                        <option value="9">9 часов</option>
                        <option value="10">10 часов</option>
                        <option value="11">11 часов</option>
                        <option value="12">12 часов</option>
                        <option value="13">13 часов</option>
                        <option value="14">14 часов</option>
                        <option value="15">15 часов</option>
                        <option value="16">16 часов</option>
                        <option value="17">17 часов</option>
                    </select>
                </div>
            </div>

            <div class="mb-3" id="select-master">
                <label for="master_id" class="form-label">Мастер</label>
                <select class="form-select" name="master_id" id="master_id" aria-label="Мастер" required disabled>
                    <option value="" selected>Выберите мастера</option>
                </select>
            </div>

            <h4>Итоговая стоимость услуги</h4>
            <div class="inline-field">
                <div class="half">
                    <label for="cost_without_discount_label" class="form-label">Стоимость (без скидки)</label>
                    <input type="text" class="form-control" id="cost_without_discount_label" name="cost_without_discount_label" placeholder="Выберите клиента и услугу" disabled>
                </div>
                <div class="half">
                    <label for="cost_label" class="form-label">Стоимость (с учетом скидки)</label>
                    <input type="text" class="form-control" id="cost_label" name="cost_label" placeholder="Выберите клиента и услугу" disabled>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Сохранить</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('[name=client_id]').change(function (ev) {
            getCostWithDiscount();
        });

        $('[name=service_id]').change(function (ev) {
            if($(ev.currentTarget).val()) {
                $('[name=date]').removeAttr('disabled');
            } else {
                $('[name=date]').attr('disabled', 1);
                $('[name=hour]').attr('disabled', 1);
                $('[name=master_id]').attr('disabled', 1);

                $('[name=date]').val('').trigger('change');
                $('[name=hour]').val('').trigger('change');
                $('[name=master_id]').val('').trigger('change');
            }

            getFreeMaster();
            getCostWithDiscount();
        });

        $('[name=date]').change(function (ev) {
            if($(ev.currentTarget).val()) {
                $('[name=hour]').removeAttr('disabled');
            } else {
                $('[name=hour]').attr('disabled', 1);
                $('[name=master_id]').attr('disabled', 1);

                $('[name=hour]').val('');
                $('[name=master_id]').val('').trigger('change');
            }

            getFreeMaster();
        })

        $('[name=hour]').change(function (ev) {
            if(!$(ev.currentTarget).val()) {
                $('[name=master_id]').attr('disabled', 1);
                $('[name=master_id]').val('').trigger('change');
            }

            getFreeMaster();
        })


        function getFreeMaster() {
            const d = $('form').serializeArray();
            let data = {};
            for(let obj of d) {
                data[obj.name] = obj.value;
            }

            if(data['service_id'] && data['date'] && data['hour']) {
                $.ajax({
                    url: '/admin/masters/free-masters.php?service_id=' + data['service_id'] + '&date=' + data['date'] + '&hour=' + data['hour'],
                    type: 'get',
                    success: (res) => {
                        $('#master_id').empty();
                        $('#master_id').append(
                            $('<option>', {
                                'value': '',
                                'text': 'Выберите мастера',
                            })
                        )

                        if(res) {
                            res = JSON.parse(res)
                            $('[name=master_id]').removeAttr('disabled');
                            $('[name=master_id]').val('').trigger('change')
                            for(let val of res) {
                                $('#master_id').append(
                                    $('<option>', {
                                        'value': val.id,
                                        'text': val.master_fio,
                                    })
                                )
                            }
                        } else {
                            $('[name=master_id]').attr('disabled', 1);
                        }
                    },
                    error: (xhr, textStatus, error) => {
                        alert(xhr.responseText);
                    }
                });
            }
        }

        function getCostWithDiscount() {
            const d = $('form').serializeArray();
            let data = {};
            for(let obj of d) {
                data[obj.name] = obj.value;
            }

            $('[name=cost]').val('')
            $('[name=cost_label]').val('')
            $('[name=cost_without_discount]').val('')
            $('[name=cost_without_discount_label]').val('')
            if(data['client_id'] && data['service_id']) {
                $.ajax({
                    url: '/admin/records/get-cost.php?client_id=' + data['client_id'] + '&service_id=' + data['service_id'],
                    type: 'get',
                    success: (res) => {
                        res = JSON.parse(res);
                        $('[name=cost]').val(res.cost);
                        $('[name=cost_label]').val(res.cost + '₽');
                        $('[name=cost_without_discount]').val(res.cost_without_discount);
                        $('[name=cost_without_discount_label]').val(res.cost_without_discount + '₽');
                    },
                    error: (xhr, textStatus, error) => {
                        alert(xhr.responseText);
                    }
                });
            }
        }
    });
</script>

<?php  require_once __DIR__ . '/../../template/footer.php'; ?>
