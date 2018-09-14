<?php

require_once '../vendor/autoload.php';

$easyExcel = new \JinDai\EasyExcel\EasyExcel();

$fileName = "C:/wamp64/www/easyExcel/tests/write.xlsx";

class ABC
{
    public function substr($string)
    {
        return substr($string, -3);
    }
}
$title = [
    'A' => ['order_id', '订单id', 'strtolower'],
    'B' => ['name', '昵称', 'strtoupper'],
    'C' => ['account', '账号', function ($item) {
        return 'hello i\'m 匿名函数' . $item . '这这这这这';
    }],
    'D' => ['phone', '手机号', [new ABC(), 'substr']],
    'E' => ['date', '生成日期', [new \JinDai\EasyExcel\ExcelFormat(), 'time']]
];

$data = [
    [
        'order_id' => 'R45614654',
        'name' => 'jindai',
        'account' => 'cc520820lk@163.com',
        'phone' => '15068548568',
        'date' => Date('Y-m-d')
    ]
];

try {
    $data = $easyExcel->setFileName($fileName)->write()->setTitle($title)->setData($data)->download();
} catch (\JinDai\EasyExcel\Exceptions\RuntimeException $e) {
    die($e->getMessage());
}
var_dump($data);