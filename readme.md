# Description
A simple excel Package of PHP
# install
composer require jindai/easy-excel
# Usage
1. 读取excel
```php
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
//你可以在readColumn中传入自定义匿名函数或者任何一个在当前页面可以访问到的函数来处理当前列并获取处理后的值
//当然你也可以置空这个选项 只要不传入该值即可
//例如
//$readColumn = ['A','B','C'] OR $readColumn = [['A','order_id'],['B','user_name']]

try {
    //提供三种方法来获取读取的结果
    //toForeach 返回的是Generator对象 可以极小化使用内存
    //toArray 返回的是读取后返回的所有内容
    //toJson 返回的是toArray的json串
    $data = $easyExcel->read($fileName)->setReadRow(2, 150)->setReadColumn($readColumn)->setChunkNumber(150)->toForeach();

} catch (\JinDai\EasyExcel\Exceptions\RuntimeException $e) {
    die($e->getMessage());
}

foreach ($data as $value) {
    var_dump($value);
}
```

2. 导出excel
```php
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
```
# License
The Easy - Excel is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT)