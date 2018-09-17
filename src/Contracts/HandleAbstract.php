<?php

namespace JinDai\EasyExcel\Contracts;

abstract class HandleAbstract
{
    protected $fileName;

    protected $ext;

    public function getDriver()
    {
        return $this;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    protected function getExt($fileName)
    {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }

    protected abstract function handle();
}
