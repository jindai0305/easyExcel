<?php

namespace JinDai\EasyExcel\Kernel;


use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ReadBefore implements IReadFilter
{
    public $nowRow;
    public $maxRow = 0;

    public function readCell($column, $row, $worksheetName = '')
    {
        if ($this->nowRow != $row) {
            $this->maxRow++;
            $this->nowRow = $row;
        }
        return false;
    }
}