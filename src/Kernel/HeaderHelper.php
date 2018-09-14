<?php

namespace JinDai\EasyExcel\Kernel;


class HeaderHelper
{
    const HEADER_LIST = [
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls' => 'application/vnd.ms-excel',
        'csv' => 'application/vnd.ms-excel; charset=GB2312'
    ];

    public function headerXls() {
        header('Content-Type: application/vnd.ms-excel');
    }

    public function headerXlsx() {
        header('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function headerCsv() {
        header('application/vnd.ms-excel; charset=GB2312');
    }
}