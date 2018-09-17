<?php

namespace JinDai\EasyExcel;

use JinDai\EasyExcel\Exceptions\RuntimeException;

class EasyExcel
{
    private $modelMap;

    const METHODS_MAP = [
        'read' => 'ReadExcel',
        'write' => 'WriteExcel'
    ];

    public function __call($method, $arg)
    {
        if (!in_array($method, array_keys(self::METHODS_MAP))) {
            throw new RuntimeException('call a not define Method ' . $method);
        }

        if (!isset($this->modelMap[$method]) || empty($this->modelMap[$method])) {
            $modelClass = __NAMESPACE__ . '\\Kernel\\' . self::METHODS_MAP[$method];
            $this->modelMap[$method] = new $modelClass();
        }

        $this->modelMap[$method]->setFileName($arg ? array_shift($arg) : "");

        return $this->modelMap[$method]->getDriver();
    }
}