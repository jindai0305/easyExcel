<?php

namespace JinDai\EasyExcel;

use JinDai\EasyExcel\Exceptions\FileNotExistExcePtion;
use JinDai\EasyExcel\Exceptions\NotAllowExtExcePtion;

class EasyExcel {

	private $fileName, $config, $fileExt, $model;
	const METHODS_MAP = ['read' => 'ReadExcel'];

	public function __construct($config) {
		$this->config = $config;
	}

	public function setFile($fileName) {
		if (!is_file($fileName)) {
			throw new FileNotExistExcePtion("File $file_name Not Exist");
		}
		$this->fileName = $fileName;
		$this->fileExt = $this->getExt();

		if (!in_array($this->fileExt, $this->config['ext'])) {
			throw new NotAllowExtExcePtion($this->fileExt . 'is not allow in config');
		}

		return $this;
	}

	private function getExt() {
		return pathinfo($this->fileName, PATHINFO_EXTENSION);
	}

	public function __call($method, $arg) {
		if (!in_array($method, array_key(self::METHODS_MAP))) {
			throw new \Exception('Method ' . $method . ' Iis Not Exist');
		}
		$modelClass = __NAMESPACE__ . '\\Kernel\\' . self::METHODS_MAP[$method];
		$this->model = new $modelClass($this->fileName);

		return $this->model->handle();
	}
}