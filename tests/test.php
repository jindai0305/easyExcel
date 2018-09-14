<?php

require_once '../vendor/autoload.php';

$easyExcel = new \JinDai\EasyExcel\EasyExcel();

$fileName = "C:/wamp64/www/easyExcel/tests/abc.xlsx";

$readColumn = [
    ['A', 'order_id', 'strtolower'], ['B', 'user_name', 'strtoupper'], ['C', 'account', function ($item) {
        return $item . '这是匿名函数';
    }]];
try {
    $data = $easyExcel->setFile($fileName)->read()->setStartRow(2)->setReadColumn($readColumn)->toArray();
}catch (\JinDai\EasyExcel\Exceptions\RuntimeException $e) {
    die($e->getMessage());
}
var_dump($data);
foreach ($data as $value) {
    var_dump($value);
}