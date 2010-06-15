<?php

class WriteStream {
	private $streamData;
	private $endPointer;
	
	public function __construct() {
		$this->streamData = array();
		$this->endPointer = 0;
	}
	
	public function writeChar($char = 0) {
		if(gettype($char) == 'string') {
			$char = ord($char);
		} else {
			$char = $char & 0xff;
		}
		
		$this->streamData[$this->endPointer] = $char;
		$this->endPointer++;
	}
	
	public function write($data = 0) {
		$stringRepresentation = (string) $data;
		
		for($currentCharacter = 0; $currentCharacter < strlen($stringRepresentation); $currentCharacter++) {
			$this->writeChar(substr($stringRepresentation, $currentCharacter, 1));
		}
	}
	
	public function __toString() {
		$stringRepresentation = '';
		
		for($currentIndex = 0; $currentIndex < $this->endPointer; $currentIndex++) {
			$stringRepresentation .= chr($this->streamData[$currentIndex]);
		}
		
		return $stringRepresentation;
	}
}

?>