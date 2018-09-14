<?php
/**
 * Created by PhpStorm.
 * User: lik
 * Date: 2018/9/14
 * Time: 14:27
 */

namespace JinDai\EasyExcel\Kernel;

trait Helper
{
    public function getExt($fileName) {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }
}