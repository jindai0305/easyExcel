<?php

namespace JinDai\EasyExcel\Kernel;

use JinDai\EasyExcel\Exceptions\RuntimeException;

trait Helper
{
    public function getFormatItem($item)
    {
        $length = count($item);
        if ($length === 1) {
            return [$item, $item, $this->getDefaultClosure()];
        }
        if ($length === 2) {
            list($sheetKey, $arrayKey) = $item;
            if (is_callable($arrayKey)) {
                return [$sheetKey, $sheetKey, $arrayKey];
            } else {
                array_push($item, $this->getDefaultClosure());
                return $item;
            }
        }
        list($sheetKey, $arrayKey, $closure) = array_slice($item, 0, 3);
        if (!is_callable($closure)) {
            throw new RuntimeException('Passed a function that cannot be executed when you call setReadColumn in "' . $sheetKey . '"');
        }
        return [$sheetKey, $arrayKey, $closure];
    }

    protected function execClosure($closure, $value)
    {
        return is_array($closure) ? call_user_func($closure, $value) : $closure($value);
    }

    protected function getDefaultClosure()
    {
        return function ($item) {
            return $item;
        };
    }
}