<?php
/**
 * Страница генерирующая Отчет об оказании услуг за период
 */

// Подлкючаем конфигурационный файл
require_once __DIR__ . '/../../config.php';

// Подключаем загрузчик зависимостей
require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend as ChartLegend;
use PhpOffice\PhpSpreadsheet\Chart\Title;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Создаем экземпляр подключения к БД
    $str = 'mysql:host=' . CONFIG['DATABASE']['host'] . ';dbname=' . CONFIG['DATABASE']['dbname'];
    $db = new PDO($str, CONFIG['DATABASE']['login'], CONFIG['DATABASE']['password']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    function getData()
    {
        global $db;
        $statement = $db->prepare("
            SELECT
                CONCAT(masters.surname, ' ', masters.name, ' ', COALESCE(masters.patronymic, '')) AS master_fio,
                COUNT(records.service_id) AS count_service,
                SUM(records.cost) AS cost
            FROM records
            INNER JOIN masters
                ON masters.id = records.master_id
            WHERE records.status_record_id = 3
            GROUP BY master_id
        ");

        $statement->execute();

        $records = $statement->fetchAll(PDO::FETCH_ASSOC);
        $records = array_map(function ($el) {
            return [
                $el['master_fio'],
                $el['count_service'],
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
                records.status_record_id = 3
        ");

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC)[0]['total'];
    }

    // Создание нового экземпляра таблицы
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Установка ширины колонок
    $sheet->getColumnDimension('A')->setWidth(60);
    $sheet->getColumnDimension('B')->setWidth(30);
    $sheet->getColumnDimension('C')->setWidth(30);

    // Информация о шапке
    $arrHeader = [
        'A' => 'ФИО Мастера',
        'B' => 'Количество услуг',
        'C' => 'Сумма'
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
        $sheet->setCellValue('B'.$row, 'Итого:');
        $sheet->getStyle('B'.$row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        $sheet->setCellValue('C'.$row, $totalCost);
        $sheet->getStyle('C'.$row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);
    }

    /* Создание диграммы => */

    // Установка данных для диаграммы
    $dataSeriesLabels = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$' . (count($data) + 1), NULL, (count($data) + 1)), // ФИО мастера
    ];
    $xAxisTickValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$' . (count($data) + 1), NULL, (count($data) + 1)), // ФИО мастера
    ];
    $dataSeriesValues = [
        new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$B$2:$B$' . (count($data) + 1), NULL, (count($data) + 1)), // Количество услуг
    ];

    // Добавление данных в диаграмму
    $series = new DataSeries(
        DataSeries::TYPE_BARCHART, // тип диаграммы
        DataSeries::GROUPING_STANDARD,
        range(0, count($dataSeriesValues) - 1), // индексы
        $dataSeriesLabels, // метки
        $xAxisTickValues, // ось X
        $dataSeriesValues // значения
    );

    $series->setPlotDirection(DataSeries::DIRECTION_COL);

    $plotArea = new PlotArea(null, [$series]);
    $legend = new ChartLegend(ChartLegend::POSITION_RIGHT, null, false);

    $title = new Title('Объем работ');

    // Создание диаграммы
    $chart = new Chart(
        'bar',
        $title,
        $legend,
        $plotArea,
        true,
        DataSeries::EMPTY_AS_GAP,
        null,
        null
    );

    // Добавление диаграммы в лист
    $chart->setTopLeftPosition('E1');
    $chart->setBottomRightPosition('M15');
    $sheet->addChart($chart);

    // Создание объекта для записи в буфер
    $writer = new Xlsx($spreadsheet);
    $writer->setIncludeCharts(true);
    ob_start();
    $writer->save('php://output');
    $xlsData = ob_get_contents();
    ob_end_clean();

    // Отправка заголовков HTTP для скачивания файла
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Отчет об объеме работ мастеров.xlsx");

    // Отправка данных файла в браузер пользователя
    echo $xlsData;
    exit;
} else {
    http_response_code(405);
    echo 'Проищошла ошибка, обратитесь к администратору сервера';
}
?>

