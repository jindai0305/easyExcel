<?php

namespace JinDai\EasyExcel\Kernel;

use JinDai\EasyExcel\Contracts\HandleAbstract;
use JinDai\EasyExcel\Exceptions\RuntimeException;

class ReadExcel extends HandleAbstract
{
    use Helper;

    private $chunkNumber = 0;

    private $startRow = 1;

    private $endRow = 0;

    private $readSheetMap = '*';

    private $driver;

    private $driverMap;

    private $filterDriver;

    private $chunkGenerator;

    private $allowExt = ['xls', 'xlsx', 'csv'];

    private $data = [];

    private $workSheet;

    private $needAssign = false;

    private $defaultMethods = [
        'time' => 'formatTime'
    ];

    public function setReadRow($startRow = 1, $endRow = 0)
    {
        if ($startRow <= 0) {
            throw new RuntimeException('startRow must > 0');
        }
        if ($endRow < 0) {
            throw new RuntimeException('endRow must > 0');
        }
        if ($endRow < $startRow) {
            throw new RuntimeException('endRow must > startRow');
        }
        $this->startRow = $startRow;
        $this->endRow = $endRow;
        return $this;
    }

    public function setChunkNumber(int $number)
    {
        if ($number <= 0) {
            throw new RuntimeException('chunkNum must > 0');
        }
        $this->chunkNumber = $number;
        return $this;
    }

    public function setReadColumn($readSheetMap)
    {
        if ($readSheetMap) {
            $this->readSheetMap = $readSheetMap;
            $this->formatNeedColumn();
        }
        return $this;
    }

    private function init()
    {
        $this->filterParams();
        $this->formatNeedColumn();
        $this->initReadDriverObject();
        $this->initReadBefore();
        $this->initChunkGenerator();
        $this->initReadFilterDriver();
    }

    private function filterParams()
    {
        if (!$this->fileName || !file_exists($this->fileName)) {
            throw new RuntimeException('Please enter an correct file name');
        }
        $this->ext = $this->getExt($this->fileName);
        if (!\in_array($this->ext, $this->allowExt)) {
            throw new RuntimeException('not a real excel file');
        }
    }

    private function formatNeedColumn()
    {
        if ($this->readSheetMap === '*') {
            return;
        }
        $this->needAssign = true;
        !is_array($this->readSheetMap) && $this->readSheetMap = [$this->readSheetMap];
        $this->readSheetMap = array_map(function ($item) {
            return $this->getFormatItem($item);
        }, $this->readSheetMap);
    }

    private function initReadFilterDriver()
    {
        if (!isset($this->filterDriver) || empty($this->filterDriver)) {
            $this->filterDriver = new ReadFilter();
        }
    }

    private function initReadDriverObject()
    {
        if (!isset($this->driverMap[$this->ext]) || empty($this->driverMap[$this->ext])) {
            $className = '\\PhpOffice\\PhpSpreadsheet\\Reader\\' . ucfirst($this->ext);
            $this->driverMap[$this->ext] = new $className();
            $this->driverMap[$this->ext]->setReadDataOnly(true);
        }
        $this->driver = $this->driverMap[$this->ext];
        if (!$this->driver || $this->driver == null) {
            throw new RuntimeException('can\'t load driver');
        }
    }

    private function initReadBefore()
    {
        $beforeObj = new ReadBefore();
        $this->driver->setReadFilter($beforeObj);
        $this->driver->load($this->fileName);
        if ($this->endRow === 0 || $this->endRow > $beforeObj->maxRow) {
            $this->endRow = $beforeObj->maxRow;
        }
        if ($this->startRow > $this->endRow) {
            $this->startRow = $this->endRow;
        }
    }

    private function initChunkGenerator()
    {
        $this->chunkGenerator = new ChunkGenerator($this->startRow, $this->endRow, $this->chunkNumber);
    }

    public function toForeach()
    {
        $this->init();
        foreach ($this->chunkGenerator as $value) {
            list($start, $end) = $value;
            $this->beforeEachGenerator($start, $end);
            for ($i = $start; $i <= $end; $i++) {
                yield $this->eachRow($i);
            }
            $this->afterEachGenerator();
        }
    }

    public function toArray()
    {
        $this->init();
        if (count($this->data)) {
            $this->data = [];
        }
        foreach ($this->chunkGenerator as $value) {
            list($start, $end) = $value;
            $this->beforeEachGenerator($start, $end);
            for ($i = $start; $i <= $end; $i++) {
                $this->data[] = $this->eachRow($i);
            }
            $this->afterEachGenerator();
        }
        return $this->data;
    }

    public function toJson()
    {
        return \json_encode($this->toArray());
    }

    private function beforeEachGenerator($start, $end)
    {
        $this->filterDriver->setChunkRow($start, $end);
        $this->driver->setReadFilter($this->filterDriver);
        $this->workSheet = $this->driver->load($this->fileName)->getActiveSheet();
    }

    public function afterEachGenerator()
    {
        return;
    }

    protected function handle()
    {
        $this->initReadFilterDriver();
        $this->filterDriver->setChunkRow($this->startRow, $this->endRow);
    }

    private function eachRow($i)
    {
        $data = [];
        if ($this->needAssign) {
            foreach ($this->readSheetMap as list($sheetKey, $arrayKey, $closure)) {
                $data[$arrayKey] = $this->readOneColumn($sheetKey, $i, $closure);
            }
        } else {
            for ($j = 'A'; $j <= $this->highestColumn; $j++) {
                $data[$j] = $this->workSheet->getCell($j . $i)->getValue();
            }
        }
        return $data;
    }

    private function readOneColumn($sheetKey, $row, $closure)
    {
        $value = is_numeric($sheetKey) ? $this->workSheet->getCellByColumnAndRow($sheetKey, $row)->getValue() : $this->workSheet->getCell($sheetKey . $row)->getValue();
        return $this->execClosure($closure, $value);
    }
}
