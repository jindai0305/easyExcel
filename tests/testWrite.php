<?php

require_once '../vendor/autoload.php';

$easyExcel = new \JinDai\EasyExcel\EasyExcel();

$fileName = "C:/wamp64/www/easyExcel/tests/write.csv";

//$readColumn = [
//    ['A', 'order_id', 'strtolower'],
//    ['B', 'user_name', 'strtoupper'],
//    ['C', 'account', function ($item) {
//        return 'hello i\'m 匿名函数' . $item . '这这这这这';
//    }],
//    ['D', 'phone', [new ABC(), 'substr']],
//    ['E', 'time', [new \JinDai\EasyExcel\ExcelFormat(), 'time']]
//];
$title = [
    'A' => ['order_id', '订单id'],
    'B' => ['name', '昵称'],
    'C' => ['account', '账号'],
    'D' => ['phone', '手机号'],
];

$data = [
    [
        'order_id' => 'R45614654',
        'name' => 'jindai',
        'account' => 'cc520820lk@163.com',
        'phone' => '15068548568',
    ]
];

try {
    $data = $easyExcel->setFileName($fileName)->write()->setTitle($title)->setData($data)->download();
} catch (\JinDai\EasyExcel\Exceptions\RuntimeException $e) {
    die($e->getMessage());
}
var_dump($data);