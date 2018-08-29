<?php
namespace JinDai\EasyExcel\Kernel;

use JinDai\EasyExcel\Contracts\HandleInterface;

class ReadExcel implements HandleInterface {

	private $fileName;

	public function __construct($fileName) {
		$this->fileName = $fileName;
	}

	public function handle() {

	}
}