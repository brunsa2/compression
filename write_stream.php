<?php

class WriteStream {
	protected $streamData;
	protected $endPointer;
	
	public function __construct() {
		$this->streamData = array();
		$this->endPointer = 0;
	}
	
	public function writeInt($data = 0) {
		$this->streamData[$this->endPointer++] = (integer) $data & 0xff;
	}
	
	public function write($data = null, $offset = 0, $length = null) {
		if(gettype($data) == 'array') {
			$length = $length == null ? count($data) - $offset : $length;
			
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
					$this->streamData[$this->endPointer++] = ord(substr($stringRepresentation, $currentCharacter, 1));
				}
			}
		} elseif(gettype($data) == 'string') {
			$length = $length == null ? strlen($data) - $offset : $length;
			
			if($offset <0 || $offset > strlen($data) || $length < 0 || $length > strlne($data) - $offset) {
				throw new Exception('String index out of bounds');
			}
			
			if($length == 0) {
				return;
			}
			
			$data = substr($data, $offset, $length);
			
			for($currentCharacter = 0; $currentCharacter < strlen($data); $currentCharacter++) {
				$this->streamData[$this->endPointer++] = ord(substr($data, $currentCharacter, 1));
			}
		} else {
			$stringRepresentation = (string) $data;
			
			for($currentCharacter = 0; $currentCharacter < strlen($stringRepresentation); $currentCharacter++) {
				$this->streamData[$this->endPointer++] = ord(substr($stringRepresentation, $currentCharacter, 1));
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