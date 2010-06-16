<?php

class ReadStream {
	private $streamData;
	private $endPointer;
	private $size;
	private $markPosition;
	
	public function __construct($data = null) {
		$this->streamData = array();
		$this->endPointer = 0;
		$this->markPosition = -1;
		
		$data = (string) $data;
		
		for($currentCharacter = 0; $currentCharacter < strlen($data); $currentCharacter++) {
			$this->streamData[$currentCharacter] = ord(substr($data, $currentCharacter, 1));
		}
		
		$this->size = strlen($data);
	}
	
	public function reset() {
		if(!$this->isClosed()) {
			throw new Exception('ReadStream has been closed.');
		}
		
		$this->endPointer = $this->markPosition == -1 ? 0 : $this->markPosition;
	}
	
	public function read() {
		return $this->atEnd() ? -1 : $dataFromBufferHead = $this->streamData[$this->endPointer++];
	}
	
	public function peek() {
		return $this->atEnd() ? -1 : $dataFromBufferHead = $this->streamData[$this->endPointer];
	}
	
	public function readToArray(array &$buffer, $offset = 0, $length = null) {
		if($this->isClosed()) {
			throw new Excpetion('ReadStream has been closed');
		}
		
		$length = $length == null ? $this->size - $this->endPointer : $length;
		
		if($offset < 0 || $length < 0) {
			throw new Exception('Array index out of bounds');
		}
		
		if($length == 0) {
			return 0;
		}
		
		if($this->atEnd()) {
			return -1;
		}
		
		$length = $this->endPointer + $length > $this->size ? $this->size : $this->endPointer + $length;
		
		for($currentIndex = $offset; $currentIndex < $offset + $length; $currentIndex++) {
			$buffer[$currentIndex] = $this->streamData[$this->endPointer++];
		}
		
		return $length;
	}
	
	public function readChar() {
		return $this->atEnd() ? null : $dataFromBufferHead = chr($this->streamData[$this->endPointer++]);
	}
	
	public function readString($length = 1) {
		if($length < 0) {
			throw new Exception('String index out of bounds');
		}
		
		$length = $length > $this->size - $this->endPointer ? $this->size - $this->endPointer : $length;
		
		$readString = '';
		for($currentCharacter = 0; $currentCharacter < $length; $currentCharacter++) {
			$readString .= chr($this->streamData[$this->endPointer++]);
		}
		
		return $readString;
	}
	
	public function peekChar() {
		return $this->atEnd() ? null : $dataFromBufferHead = chr($this->streamData[$this->endPointer]);
	}
	
	public function readCharsToArray(array &$buffer, $offset = 0, $length = null) {
		if($this->isClosed()) {
			throw new Excpetion('ReadStream has been closed');
		}
		
		$length = $length == null ? $this->size - $this->endPointer : $length;
		
		if($offset < 0 || $length < 0) {
			throw new Exception('Array index out of bounds');
		}
		
		if($length == 0) {
			return 0;
		}
		
		if($this->atEnd()) {
			return -1;
		}
		
		$length = $this->endPointer + $length > $this->size ? $this->size : $this->endPointer + $length;
		
		for($currentIndex = $offset; $currentIndex < $offset + $length; $currentIndex++) {
			$buffer[$currentIndex] = chr($this->streamData[$this->endPointer++]);
		}
		
		return $length;
	}
	
	public function mark($position = 0) {
		if(!$this->isClosed()) {
			$this->markPosition = $position;
		} else {
			throw new Exception('ReadStream has been closed.');
		}
	}
	
	public function skip($length = 1) {
		if(!$this->isClosed()) {
			throw new Excpetion('ReadStream has been closed.');
		}
		
		$minimumSkip = -$this->endPointer;
		$maximumSkip = $this->size - $this->endPointer;
		
		if($maximumSkip == 0 || $length > $maximumSkip) {
			$length = $maximumSkip;
		} elseif($length < $minimumSkip) {
			$length = $minimumSkip;
		}
		
		$this->endPointer += $length;
		return $length;
	}
	
	public function close() {
		unset($this->streamData);
	}
	
	public function isClosed() {
		return isset($this->streamData);
	}
	
	public function atEnd() {
		return $this->size == $this->endPointer;
	}
	
	public function __toString() {
		$stringRepresentation = '';
		
		for($currentIndex = 0; $currentIndex < $this->size; $currentIndex++) {
			$stringRepresentation .= chr($this->streamData[$currentIndex]);
		}
		
		return $stringRepresentation;
	}
}

?>