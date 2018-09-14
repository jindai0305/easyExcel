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
        $this->init();
    }

    public function handle()
    {
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
                if (is_numeric($sheetKey)) {
                    $data[$arrayKey] = $closure($this->workSheet->getCellByColumnAndRow($sheetKey, $i)->getValue());
                } else {
                    $data[$arrayKey] = $closure($this->workSheet->getCell($sheetKey . $i)->getValue());
                }
            }
        } else {
            for ($j = 'A'; $j <= $this->highestColumn; $j++) {
                $data[$j] = $this->workSheet->getCell($j . $i)->getValue();
            }
        }
        return $data;
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
        if ($this->needAssign) {
            $this->readSheetMap = array_map(function ($item) {
                if (is_array($item)) {
                    $length = count($item);
                    if ($length == 1) {
                        array_push($item, $item[0]);
                    } else if ($length == 2) {
                        if ($item[1] instanceof \Closure) {
                            array_push($item, $item[1]);
                            $item[1] = $item[0];
                        } else {
                            array_push($item, $this->getDefaultClosure());
                        }
                    } else {
                        $closure = $item[2];
                        if (is_string($closure)) {
                            if (in_array(strtolower($closure), array_keys($this->defaultMethods))) {
                                $item[2] = $this->defaultMethods[$closure];
                            } else {
                                if (!is_callable($closure)) {
                                    $item[2] = $this->getDefaultClosure();
                                }
                            }
                        } elseif (!$closure instanceof \Closure) {
                            array_pop($item);
                            array_push($item, $this->getDefaultClosure());
                        }
                    }
                } else {
                    $item = [$item, $item, $this->getDefaultClosure()];
                }
                return $item;
            }, $this->readSheetMap);
        }
    }

    private function getDefaultClosure()
    {
        return function ($item) {
            return $item;
        };
    }
}