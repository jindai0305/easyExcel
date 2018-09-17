<?php

namespace JinDai\EasyExcel\Kernel;


use JinDai\EasyExcel\Exceptions\RuntimeException;

class HeaderHelper
{
    const HEADER_LIST = [
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls' => 'application/vnd.ms-excel',
        'csv' => 'application/vnd.ms-excel; charset=GB2312'
    ];

    public function setHeader(string $ext)
    {
        if (!in_array($ext, array_keys(self::HEADER_LIST))) {
            throw new RuntimeException('can\'t download as "' . $ext . '" file');
        }
        $this->{'header' . ucfirst($ext)}();
    }

    public function headerXls()
    {
        header('Content-Type: application/vnd.ms-excel');
    }

    public function headerXlsx()
    {
        header('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function headerCsv()
    {
        header('application/vnd.ms-excel; charset=GB2312');
    }
}