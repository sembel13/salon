<?php
/**
 * Страница генерирующая Отчет об оказании услуг за период
 */

// Подлкючаем конфигурационный файл
require_once __DIR__ . '/../../config.php';

// Подключаем загрузчик зависимостей
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_REQUEST['date_start']) && isset($_REQUEST['date_end'])) {
        // Создаем экземпляр подключения к БД
        $str = 'mysql:host=' . CONFIG['DATABASE']['host'] . ';dbname=' . CONFIG['DATABASE']['dbname'];
        $db = new PDO($str, CONFIG['DATABASE']['login'], CONFIG['DATABASE']['password']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        function getData()
        {
            global $db;
            $statement = $db->prepare("
                SELECT
                    services.name AS service_name,
                    CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio,
                    CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio,
                    DATE_FORMAT(records.date, '%d.%m.%Y') AS human_date,
                    records.cost
                FROM records
                INNER JOIN clients
                    ON clients.id = records.client_id
                INNER JOIN masters
                    ON masters.id = records.master_id
                INNER JOIN services
                    ON services.id = records.service_id
                INNER JOIN status_record
                    ON status_record.id = records.status_record_id
                WHERE
                    records.date BETWEEN :date_start AND :date_end
                    AND records.status_record_id IN (1, 3)
            ");

            $statement->execute([
                'date_start' => $_REQUEST['date_start'],
                'date_end' => $_REQUEST['date_end']
            ]);

            $records = $statement->fetchAll(PDO::FETCH_ASSOC);
            $records = array_map(function ($el) {
                return [
                    $el['service_name'],
                    $el['master_fio'],
                    $el['client_fio'],
                    $el['human_date'],
                    $el['cost']
                ];
            }, $records);

            return $records;
        }

        function getTotal() {
            global $db;
            $statement = $db->prepare("
                SELECT
                    SUM(records.cost) AS total
                FROM records
                WHERE
                    records.date BETWEEN :date_start AND :date_end
                    AND records.status_record_id IN (1, 3)
            ");

            $statement->execute([
                'date_start' => $_REQUEST['date_start'],
                'date_end' => $_REQUEST['date_end']
            ]);

            return $statement->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
        }

        // Создание нового экземпляра таблицы
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Установка ширины колонок
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(60);
        $sheet->getColumnDimension('C')->setWidth(60);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);

        // Информация о шапке
        $arrHeader = [
            'A' => 'Улсуга',
            'B' => 'Специалист',
            'C' => 'Клиент',
            'D' => 'Дата',
            'E' => 'Стоимость'
        ];

        // Заполняем шапку
        foreach ($arrHeader as $key => $item) {
            $sheet->setCellValue($key . '1', $item);
            $sheet->getStyle($key . '1')->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        // Пример данных для таблицы
        $data = getData();

        // Заполнение данными таблицы
        $row = 2;
        foreach ($data as $rowData) {
            $col = 'A';
            foreach ($rowData as $cellData) {
                $sheet->setCellValue($col . $row, $cellData);
                // Установка границ для каждой ячейки
                $sheet->getStyle($col . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $col++;
            }
            $row++;
        }

        // Заполняем итоговую сумму
        $totalCost = getTotal();
        if(!is_null($totalCost)) {
            $sheet->setCellValue('D'.$row, 'Итого:');
            $sheet->getStyle('D'.$row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);

            $sheet->setCellValue('E'.$row, $totalCost);
            $sheet->getStyle('E'.$row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
        }

        // Создание объекта для записи в буфер
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Отправка заголовков HTTP для скачивания файла
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Отчет об оказании услуг за период.xlsx");

        // Отправка данных файла в браузер пользователя
        echo $xlsData;
        exit;
    } else {
        http_response_code(405);
        echo 'Проищошла ошибка, обратитесь к администратору сервера';
    }
} else {
    http_response_code(405);
    echo 'Проищошла ошибка, обратитесь к администратору сервера';
}
?>

