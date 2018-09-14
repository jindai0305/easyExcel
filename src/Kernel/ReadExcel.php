<?php

namespace JinDai\EasyExcel\Kernel;

use JinDai\EasyExcel\Exceptions\RuntimeException;
use JinDai\EasyExcel\Contracts\HandleInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ReadExcel implements HandleInterface
{
    use Helper;

    private $fileName;

    private $driver;

    private $ext;

    private $allowExt = ['xls', 'xlsx'];

    private $data = [];

    private $workSheet;

    private $highestRow;

    private $highestColumn;

    private $startRow = 1;

    private $readSheetMap = '*';

    private $needAssign = false;

    private $defaultMethods = [
        'time' => 'formatTime'
    ];

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function handle()
    {
        $this->init();
        if (!$this->driver || $this->driver == null) {
            throw new RuntimeException('can\'t load driver');
        }
        $this->driver = $this->driver->load($this->fileName);
        $this->workSheet = $this->driver->getActiveSheet();

        $this->highestRow = $this->workSheet->getHighestRow();

        if ($this->highestRow < $this->startRow) {
            throw new RuntimeException('file is empty');
        }

        $this->highestColumn = $this->workSheet->getHighestColumn();
        return $this;
    }

    public function setReadColumn($needReadSheetMap)
    {
        if ($needReadSheetMap) {
            $this->readSheetMap = $needReadSheetMap;
            $this->formatNeedColumn();
        }
        return $this;
    }

    public function setStartRow($startRow)
    {
        $this->startRow = $startRow;
        return $this;
    }

    public function toForeach()
    {
        for ($i = $this->startRow; $i <= $this->highestRow; $i++) {
            yield $this->eachRow($i);
        }
    }

    public function toArray()
    {
        for ($i = $this->startRow; $i <= $this->highestRow; $i++) {
            $this->data[] = $this->eachRow($i);
        }
        return $this->data;
    }

    public function toJson()
    {
        $this->toArray();
        return \json_encode($this->data);
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
        return is_array($closure) ? call_user_func($closure, $value) : $closure($value);
    }

    private function init()
    {
        if (!file_exists($this->fileName)) {
            throw new RuntimeException('file ' . $this->fileName . ' not exists');
        }
        $this->ext = $this->getExt($this->fileName);
        if (!\in_array($this->ext, $this->allowExt)) {
            throw new RuntimeException('not a real excel file');
        }
        $this->driver = $this->getDriverObject();
    }

    private function getDriverObject()
    {
        return $this->ext === 'xls' ? new Xls() : new Xlsx();
    }

    private function formatNeedColumn()
    {
        $this->needAssign = $this->readSheetMap !== '*';
        if ($this->needAssign && !is_array($this->readSheetMap)) {
            $this->readSheetMap = [$this->readSheetMap];
        }
        $this->formatReadSheetMap();
    }

    private function formatReadSheetMap()
    {
        if (!$this->needAssign) {
            return;
        }
        $this->readSheetMap = array_map(function ($item) {
            if (is_array($item)) {
                return $this->getFormatItem($item);
            }
            return [$item, $item, $this->getDefaultClosure()];
        }, $this->readSheetMap);
    }
}