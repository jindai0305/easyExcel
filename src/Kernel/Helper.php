<?php
/**
 * Created by PhpStorm.
 * User: lik
 * Date: 2018/9/14
 * Time: 14:27
 */

namespace JinDai\EasyExcel\Kernel;

use JinDai\EasyExcel\Exceptions\RuntimeException;

trait Helper
{
    public function getExt($fileName)
    {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }

    public function getFormatItem($item)
    {
        $length = count($item);
        if ($length === 1) {
            return [$item, $item, $this->getDefaultClosure()];
        }
        if ($length == 2) {
            list($sheetKey, $arrayKey) = $item;
            if ($arrayKey instanceof \Closure) {
                return [$sheetKey, $sheetKey, $arrayKey];
            } else {
                array_push($item, $this->getDefaultClosure());
                return $item;
            }
        }
        list($sheetKey, $arrayKey, $closure) = array_slice($item,0,3);
        if (!is_callable($closure)) {
            throw new RuntimeException('Passed a function that cannot be executed when you call setReadColumn in "' . $sheetKey . '"');
        }
        return [$sheetKey, $arrayKey, $closure];
    }

    protected function getDefaultClosure()
    {
        return function ($item) {
            return $item;
        };
    }
}