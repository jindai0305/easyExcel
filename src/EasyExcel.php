<?php

namespace JinDai\EasyExcel;

use JinDai\EasyExcel\Exceptions\RuntimeException;

class EasyExcel
{
    private $fileName, $config, $model;
    const METHODS_MAP = ['read' => 'ReadExcel'];

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function setFile($fileName)
    {
        if (!is_file($fileName)) {
            throw new RuntimeException('File' . $fileName . 'Not Exist');
        }
        $this->fileName = $fileName;

        return $this;
    }

    public function __call($method, $arg)
    {
        if (!in_array($method, array_keys(self::METHODS_MAP))) {
            throw new RuntimeException('call a not define Method ' . $method);
        }
        $modelClass = __NAMESPACE__ . '\\Kernel\\' . self::METHODS_MAP[$method];
        $this->model = new $modelClass($this->fileName);

        return $this->model->handle();
    }
}