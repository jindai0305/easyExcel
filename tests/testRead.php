<?php

require_once '../vendor/autoload.php';

$easyExcel = new \JinDai\EasyExcel\EasyExcel();

$fileName = "C:/wamp64/www/easyExcel/tests/read.xlsx";

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
    ['E','time',[new \JinDai\EasyExcel\ExcelFormat(),'time']]
];

try {
    $data = $easyExcel->setFileName($fileName)->read()->setStartRow(2)->setReadColumn($readColumn)->toArray();
} catch (\JinDai\EasyExcel\Exceptions\RuntimeException $e) {
    die($e->getMessage());
}
var_dump($data);