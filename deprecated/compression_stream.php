<?php

class CompressionStream3 {
	private $outputStream;
	
	public function __construct(ReadStream $inputStream) {
		$encoder = new RangeEncoder2(new WriteStream());
		$model = new Order0Model2();

		while(!$inputStream->atEnd()) {
			$symbol = $inputStream->read();
			
			$encoder->encode($model->getLowFrequency($symbol), $model->getHighFrequency($symbol), $model->getFrequencyRange());
			$model->updateModel($symbol);
		}
		
		$encoder->flush();
		
		$this->outputStream = $encoder->getStream();
	}
	
	public function getStream() {
		return $this->outputStream;
	}
	
	public function __toString() {
		return (string) $this->outputStream;
	}
}

?>