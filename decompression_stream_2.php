<?php

class DecompressionStream2 extends ReadStream {
	protected $decoder;
	protected $model;
	
	protected $outputStream;
	
	public function __construct() {
		parent::construct();
		
		$this->decoder = new RangeDecoder2();
		$this->model = new Order0Model();
		
		$this->outputStream = $this->encoder->getStream();
	}
}

?>