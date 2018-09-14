<?php

namespace JinDai\EasyExcel;

class ExcelFormat
{
    const START_TIME = 25569;

    public function time($value)
    {
        if (!$timestamp = strtotime($value)) {
            $timestamp = is_numeric($value) ? ($value - self::START_TIME) * 24 * 60 * 60 : 0;
        }
        return date('Y-m-d H:i:s', $timestamp);
    }
}