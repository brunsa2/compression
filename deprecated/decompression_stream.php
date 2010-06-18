<?php

class DecompressionStream3 {
	private $outputStream;
	
	public function __construct(ReadStream $inputStream) {
		$decoder = new RangeDecoder2($inputStream);
		$model = new Order0Model2();
		
		$this->outputStream = new WriteStream();
		
		$nextByte = 0;
		
		while(!$inputStream->atEnd()) {
			$count = $decoder->getCode($model->getFrequencyRange());
			
			$symbol = Order0Model::NUMBER_OF_SYMBOLS - 1;
			for(; $model->getLowFrequency($symbol) > $count; $symbol--);
			$nextByte = $symbol == Order0Model::NUMBER_OF_SYMBOLS - 1 ? -1 : $symbol;
			
			$decoder->decode($model->getLowFrequency($symbol), $model->getHighFrequency($symbol), $model->getFrequencyRange());
			
			$model->updateModel($symbol);
			
			$this->outputStream->writeInt($symbol);
		}
		
		$table = $model->getFrequencyTable();
		
		for($currentSymbol = 0; $currentSymbol < Order0Model::NUMBER_OF_SYMBOLS; $currentSymbol++) {
			echo chr($currentSymbol) . ': ' . ($table[$currentSymbol + 1] - $table[$currentSymbol]) . '<br />';
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