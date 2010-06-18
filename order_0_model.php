<?php

class Order0Model2 {
	const NUMBER_OF_SYMBOLS = 257;
	
	private $frequencies;
	
	public function __construct() {
		$this->frequencies = array();
		
		for($currentSymbol = 0; $currentSymbol < self::NUMBER_OF_SYMBOLS + 1; $currentSymbol++) {
			$this->frequencies[$currentSymbol] = $currentSymbol;
		}
	}
	
	public function updateModel($symbol) {
		for($currentSymbol = $symbol + 1; $currentSymbol < self::NUMBER_OF_SYMBOLS + 1; $currentSymbol++) {
			$this->frequencies[$currentSymbol]++;
		}
		
		$this->rescaleFrequencies();
	}
	
	private function rescaleFrequencies() {
		if($this->frequencies[self::NUMBER_OF_SYMBOLS] > RangeDecoder2::MAXIMUM_RANGE) {
			$total = 0;
			
			for($currentSymbol = 1; $currentSymbol < self::NUMBER_OF_SYMBOLS - 1; $currentSymbol++) {
				$total = $total + ((   ($this->frequencies[$currentSymbol] - $this->frequencies[$currentSymbol - 1])   /2) + 1);
				$this->frequencies[$currentSymbol] = $this->frequencies[$currentSymbol - 1] + ((($this->frequencies[$currentSymbol] - $this->frequencies[$currentSymbol - 1])/2) + 1);
			}
			
			$this->frequencies[self::NUMBER_OF_SYMBOLS - 1] = $total;
		}
	}
	
	public function getLowFrequency($symbol) {
		return $this->frequencies[$symbol];
	}
	
	public function getHighFrequency($symbol) {
		return $this->frequencies[$symbol + 1];
	}
	
	public function getFrequencyRange() {
		return $this->frequencies[self::NUMBER_OF_SYMBOLS];
	}
	
	public function getFrequencyTable() {
		return $this->frequencies;
	}
}

?>