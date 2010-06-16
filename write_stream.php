<?php

class WriteStream {
	protected $streamData;
	protected $endPointer;
	
	public function __construct() {
		$this->streamData = array();
		$this->endPointer = 0;
	}
	
	public function writeInt($data = 0) {
		if(gettype($data) != 'integer') {
			throw new Exception('Integer was expected but ' . gettype($data) . ' was passed');
		} else {
			$data = $data & 0xff;
		}
		
		$this->streamData[$this->endPointer] = $data;
		$this->endPointer++;
	}
	
	public function write($data = null, $offset = 0, $length = null) {
		if(gettype($data) == 'array') {
			if($length == null) {
				$length = count($data) - $offset;
			}
			
			if($offset < 0 || $offset > count($data) || $length < 0 || $length > count($data) - $offset) {
				throw new Exception('Array index out of bounds');
			}
			
			if($length == 0) {
				return;
			}
			
			for($currentIndex = $offset; $currentIndex < $offset + $length; $currentIndex++) {
				if(!isset($data[$currentIndex])) {
					throw new Exception('Array element does not exist');
				}
				
				$stringRepresentation = (string) $data[$currentIndex];
				
				for($currentCharacter = 0; $currentCharacter < strlen($stringRepresentation); $currentCharacter++) {
					$this->streamData[$this->endPointer] = ord(substr($stringRepresentation, $currentCharacter, 1));
					$this->endPointer++;
				}
			}
		} elseif(gettype($data) == 'string') {
			if($length == null) {
				$length = strlen($data) - $offset;
			}
			//echo "$data; Length: $length; Offset: $offset<br />";
			$data = substr($data, $offset, $length);
			
			for($currentCharacter = 0; $currentCharacter < strlen($data); $currentCharacter++) {
				$this->streamData[$this->endPointer] = ord(substr($data, $currentCharacter, 1));
				$this->endPointer++;
			}
		} else {
			$stringRepresentation = (string) $data;
			
			for($currentCharacter = 0; $currentCharacter < strlen($stringRepresentation); $currentCharacter++) {
				$this->streamData[$this->endPointer] = ord(substr($stringRepresentation, $currentCharacter, 1));
				$this->endPointer++;
			}
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