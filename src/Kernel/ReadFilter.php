<?php

namespace JinDai\EasyExcel\Kernel;


use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class ReadFilter implements IReadFilter
{
    public $startRow = 1;

    public $endRow = null;

    public $readColumn = '*';

    public function readCell($column, $row, $worksheetName = '')
    {
        if ($row < $this->startRow) {
            return false;
        }
        if ($this->endRow !== null && $row > $this->endRow) {
            return false;
        }
        if ($this->readColumn === '*') {
            return true;
        }
        return in_array($column, $this->readColumn);
    }

    public function setChunkRow($startRow,$endRow) {
        $this->startRow = $startRow;
        $this->endRow = $endRow;
    }
}