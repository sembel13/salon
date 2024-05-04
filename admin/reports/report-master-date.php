<?php
/**
 * Страница генерирующая отчет о занятости специиласта на дату
 */

// Подлкючаем конфигурационный файл
require_once __DIR__ . '/../../config.php';

// Подключаем загрузчик зависимостей
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_REQUEST['master_id']) && isset($_REQUEST['date'])) {
        function getData()
        {
            // Создаем экземпляр подключения к БД
            $str = 'mysql:host=' . CONFIG['DATABASE']['host'] . ';dbname=' . CONFIG['DATABASE']['dbname'];
            $db = new PDO($str, CONFIG['DATABASE']['login'], CONFIG['DATABASE']['password']);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $statement = $db->prepare("
                SELECT
                    CONCAT(clients.surname, ' ', clients.name, ' ', COALESCE(clients.patronymic, '')) AS client_fio,
                    services.name AS service_name,
                    DATE_FORMAT(records.date, '%d.%m.%Y') AS human_date,
                    CONCAT(records.hour, ' часов') AS human_hour,
                    status_record.name AS status_record_name
                FROM records
                INNER JOIN clients
                    ON clients.id = records.client_id
                INNER JOIN services
                    ON services.id = records.service_id
                INNER JOIN status_record
                    ON status_record.id = records.status_record_id
                WHERE
                    records.master_id = :master_id
                    AND records.date = :date
            ");

            $statement->execute([
                'master_id' => $_REQUEST['master_id'],
                'date' => $_REQUEST['date']
            ]);

            $records = $statement->fetchAll(PDO::FETCH_ASSOC);
            $records = array_map(function ($el) {
                return [
                    $el['client_fio'],
                    $el['service_name'],
                    $el['human_date'],
                    $el['human_hour'],
                    $el['status_record_name']
                ];
            }, $records);

            return $records;
        }

        // Создание нового экземпляра таблицы
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Установка ширины колонок
        $sheet->getColumnDimension('A')->setWidth(60);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(30);

        // Информация о шапке
        $arrHeader = [
            'A' => 'Клиент',
            'B' => 'Услуга',
            'C' => 'Дата',
            'D' => 'Время',
            'E' => 'Статус'
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

        // Создание объекта для записи в буфер
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();

        // Отправка заголовков HTTP для скачивания файла
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Отчет о занятости специалиста.xlsx");

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

