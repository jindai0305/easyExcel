<?php

namespace JinDai\EasyExcel\Kernel;

use JinDai\EasyExcel\Contracts\HandleAbstract;
use JinDai\EasyExcel\Exceptions\RuntimeException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class WriteExcel extends HandleAbstract
{
    use Helper;

    private $path;

    private $ext;

    private $title = [];

    private $data = [];

    private $writeSheetMap;

    private $driver;

    private $sheetDriver;

    private $activeSheet;

    private $startRow = 1;

    private $headerHelper;

    public function setTitle($title)
    {
        if (!$title) {
            throw new RuntimeException('title can\'t be null');
        }
        $this->title = $title;
        $this->formatTitle($title);
        return $this;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function download()
    {
        if (!$this->headerHelper) {
            $this->headerHelper = new HeaderHelper();
        }
        $this->headerHelper->setHeader($this->ext);

        header('Content-Disposition: attachment;filename="' . $this->fileName . '"');
        header('Cache-Control: max-age=0');
        $this->createExcelOutput()->save("php://output");
    }

    public function saveTo()
    {
        $this->createExcelOutput()->save($this->path . '/' . $this->fileName);
        if (file_exists($this->path . '/' . $this->fileName)) {
            return true;
        }
        throw new RuntimeException('directory has no write permission');
    }

    private function formatExcelShellTitle()
    {
        foreach ($this->title as $key => $value) {
            $this->activeSheet->setCellValue($key . $this->startRow, $value[1]);
        }
        $this->startRow++;
    }

    protected function handle()
    {
        $this->init();

        $this->sheetDriver = new Spreadsheet();

        $this->activeSheet = $this->sheetDriver->getActiveSheet();

        return $this;
    }

    private function init()
    {
        if (!$this->fileName) {
            throw new RuntimeException('please enter fileName');
        }
        $this->path = dirname($this->fileName);
        $this->fileName = basename($this->fileName);
        $this->ext = $this->getExt($this->fileName);
    }

    private function createExcelOutput()
    {
        if (!count($this->data) || !count($this->title)) {
            throw new RuntimeException('data or title can\'t be empty');
        }
        $this->formatExcelShellTitle();
        foreach ($this->data as $item) {
            foreach ($item as $key => $value) {
                if (!isset($this->writeSheetMap[$key])) {
                    unset($this->data[$key]);
                    continue;
                }
                list($sheetKey, $closure) = $this->writeSheetMap[$key];
                $this->activeSheet->setCellValue($sheetKey . $this->startRow, $this->execClosure($closure, $value));
            }
            $this->startRow++;
        }
        return $this->getDriverObject();
    }


    private function formatTitle()
    {
        foreach ($this->title as $sheetKey => $item) {
            list($key, $title, $closure) = $this->getFormatItem($item);
            if (isset($this->sheetMap[$key])) {
                throw new RuntimeException('entered the same key in ' . $sheetKey . ' of ' . $key);
            }
            $this->writeSheetMap[$key] = [$sheetKey, $closure];
        }
    }

    private function getDriverObject()
    {
        $this->driver = IOFactory::createWriter($this->sheetDriver, ucfirst($this->ext));
        return $this->driver;
    }
}