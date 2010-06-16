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
		if($this->endPointer < $this->size) {
			$dataFromBufferHead = $this->streamData[$this->endPointer++];
		} else {
			$dataFromBufferHead = -1;
		}
		return $dataFromBufferHead;
	}
	
	public function readToArray(array &$buffer, $offset = 0, $length = null) {
		if($this->isClosed()) {
			throw new Excpetion('ReadStream has been closed');
		}
			
		if($length == null) {
			$length = $this->size - $this->endPointer;
		}
		
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
		if($this->endPointer < $this->size) {
			$dataFromBufferHead = chr($this->streamData[$this->endPointer]);
			$this->endPointer++;
		} else {
			$dataFromBufferHead = '';
		}
		
		return $dataFromBufferHead;
	}
	
	public function readCharsToArray(array &$buffer, $offset = 0, $length = null) {
		if($this->isClosed()) {
			throw new Excpetion('ReadStream has been closed');
		}
			
		if($length == null) {
			$length = $this->size - $this->endPointer;
		}
		
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
	
	public function peek() {
		if(!$this->atEnd()) {
			$dataFromBufferHead = $this->streamData[$this->endPointer];
		} else {
			$dataFromBufferHead = -1;
		}
		return $dataFromBufferHead;
	}
	
	public function peekChar() {
		if(!$this->atEnd()) {
			$dataFromBufferHead = chr($this->streamData[$this->endPointer]);
		} else {
			$dataFromBufferHead = '';
		}
		
		return $dataFromBufferHead;
	}
	
	public function close() {
		$this->streamData = null;
	}
	
	public function isClosed() {
		return $this->streamData == null;
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
		
		$this->endPointer +=  $length;
		return $length;
	}
	
	public function atEnd() {
		return $this->size == $this->endPointer;
	}
	
	public function mark($position = 0) {
		if(!$this->isClosed()) {
			$this->markPosition = $position;
		} else {
			throw new Exception('ReadStream has been closed.');
		}
	}
	
	public function __toString() {
		$stringRepresentation = '';
		
		for($currentIndex = $this->endPointer; $currentIndex < $this->size; $currentIndex++) {
			$stringRepresentation .= chr($this->streamData[$currentIndex]);
		}
		
		return $stringRepresentation;
	}
}

?>