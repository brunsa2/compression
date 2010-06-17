<?php

class CompressionStream2 extends WriteStream {
	protected $encoder;
	protected $model;
	
	protected $outputStream;
	
	public function __construct() {
		parent::__construct();
		
		$this->encoder = new RangeEncoder2(new WriteStream());
		$this->model = new Order0Model();
		
		$this->outputStream = $this->encoder->getStream();
	}
	
	public function writeChar($char = 0) {
		if(gettype($char) == 'string') {
			$char = ord($char);
		} else {
			$char = $char & 0xff;
		}
		
		$this->encoder->encode($this->model->getLowFrequency($char), $this->model->getHighFrequency($char), $this->model->getFrequencyRange());
		$this->model->updateModel($char);
	}
	
	public function flush() {
		$this->encoder->flush();
	}
	
	public function __toString() {
		return (string) $this->outputStream;
	}
}

?>