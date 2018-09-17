<?php

namespace JinDai\EasyExcel\Kernel;


class ChunkGenerator implements \Iterator
{
    private $list = [];
    private $index = 0;
    private $length = 0;

    public function __construct($start, $end, $chunk = 0)
    {
        if ($chunk === 0) {
            array_push($this->list, [$start, $end]);
        } else {
            while ($end >= $start) {
                $chunkEnd = $start + $chunk;
                array_push($this->list, [$start, $chunkEnd < $end ? $chunkEnd : $end]);
                $start += $chunk + 1;
            }
        }
        $this->length = count($this->list) - 1;
    }

    public function rewind()
    {
        $this->index = 0;
    }

    public function key()
    {
        return $this->index;
    }

    public function current()
    {
        return $this->list[$this->index];
    }

    public function next()
    {
        $this->index++;
    }

    public function valid()
    {
        return $this->index <= $this->length;
    }


}