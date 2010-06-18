<?php

class DecompressionStream {
	private $outputStream;
	
	public function __construct(ReadStream $inputStream, $forceThirtyTwoBitMath = false) {
		$decoder = new RangeDecoder($inputStream, $forceThirtyTwoBitMath);
		$model = new Order0Model();
		$this->outputStream = new WriteStream();
				
		while(!$inputStream->atEnd()) {
			$frequency = $decoder->getFrequency($model->getFrequencyRange());
			
			$symbol = 256;
			for(; $model->getLowFrequency($symbol) > $frequency; $symbol--);
			
			if($symbol == 256) {
				break;
			}
			
			$decoder->removeRange($model->getLowFrequency($symbol), $model->getHighFrequency($symbol), $model->getFrequencyRange());
			$model->update($symbol, $decoder->getMaximumRange());
			
			$this->outputStream->writeInt($symbol);
		}
	}
	
	public function getStream() {
		return $this->outputStream;
	}
	
	public function __toString() {
		return (string) $this->outputStream;
	}
}

?>