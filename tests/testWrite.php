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
        'order_id' => 'R45614650',
        'name' => 'jindai',
        'account' => 'cc520820lk@163.com',
        'phone' => '15068548568',
        'date' => Date('Y-m-d')
    ]
];

for ($i = 1; $i < 10000; $i++) {
    array_push($data, [
        'order_id' => 'R4561465' . $i,
        'name' => 'jindai' . $i,
        'account' => 'cc520820lk@163.com' . $i,
        'phone' => '1506854856' . $i,
        'date' => Date('Y-m-d')
    ]);
}

try {
    $data = $easyExcel->write($fileName)->setTitle($title)->setData($data)->download();
} catch (\JinDai\EasyExcel\Exceptions\RuntimeException $e) {
    die($e->getMessage());
}
var_dump($data);