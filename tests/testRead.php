<?php

require_once '../vendor/autoload.php';

$easyExcel = new \JinDai\EasyExcel\EasyExcel();

$fileName = __DIR__ . "/read.xlsx";

class ABC
{
    public function substr($string)
    {
        return substr($string, -3);
    }
}

$readColumn = [
    ['A', 'order_id', 'strtolower'],
    ['B', 'user_name', 'strtoupper'],
    ['C', 'account', function ($item) {
        return 'hello i\'m 匿名函数' . $item . '这这这这这';
    }],
    ['D', 'phone', [new ABC(), 'substr']],
    ['E', 'time', [new \JinDai\EasyExcel\ExcelFormat(), 'time']]
];

$before = memory_get_usage();

try {
    $data = $easyExcel->read($fileName)->setReadRow(2, 150)->setReadColumn($readColumn)->setChunkNumber(150)->toForeach();
} catch (\JinDai\EasyExcel\Exceptions\RuntimeException $e) {
    die($e->getMessage());
}
$after = memory_get_usage();

echo 'before----' . round($before / 1024 / 1024, 2) . ' MB' . "</br>";
echo 'after----' . round($after / 1024 / 1024, 2) . ' MB' . "</br>";
echo 'between----' . round(($after - $before) / 1024 / 1024, 2) . ' MB' . "</br>";

foreach ($data as $value) {
    var_dump($value);
}

