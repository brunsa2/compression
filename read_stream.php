<?php

class ReadStream {
	private $streamData;
	private $endPointer;
	private $size;
	private $markPosition;
	
	public function __construct($data = '') {
		$this->streamData = array();
		$this->endPointer = 0;
		$this->markPosition = -1;
		
		$data = (string) $data;
		
		for($currentCharacter = 0; $currentCharacter < strlen($data); $currentCharacter++) {
			$this->streamData[$currentCharacter] = ord(substr($data, $currentCharacter, 1));
		}
		
		$this->size = strlen($data);
	}
	
	public function readToArray(array &$dataArray, $startPosition = 0, $length = 1) {
		if($this->isClosed()) {
			throw new Exception('ReadStream has been closed.');
		}
		
		if($startPosition < 0) {
			throw new Exception('Start position out of bounds');
		}
		
		if($length < 0) {
			throw new Exception('Length out of bounds');
		}
		
		if($length == 0) {
			return 0;
		}
		
		if($this->size == $this->endPointer) {
			return -1;
		}
		
		$lengthToRead = $this->endPointer + $length > $this->size ? $this->size : $this->endPointer + $length;
		
		for($currentPosition = $startPosition; $currentPosition < $startPosition + $lengthToRead; $currentPosition++) {
			$dataArray[$currentPosition] = $this->streamData[$this->endPointer];
			$this->endPointer++;
		}
		
		return $lengthToRead;
	}
	
	public function readCharsToArray(array &$dataArray, $startPosition = 0, $length = 1) {
		if($this->isClosed()) {
			throw new Exception('ReadStream has been closed.');
		}
		
		if($startPosition < 0) {
			throw new Exception('Start position out of bounds');
		}
		
		if($length < 0) {
			throw new Exception('Length out of bounds');
		}
		
		if($length == 0) {
			return 0;
		}
		
		if($this->size == $this->endPointer) {
			return -1;
		}
		
		$lengthToRead = $this->endPointer + $length > $this->size ? $this->size : $this->endPointer + $length;
		
		for($currentPosition = $startPosition; $currentPosition < $startPosition + $lengthToRead; $currentPosition++) {
			$dataArray[$currentPosition] = chr($this->streamData[$this->endPointer]);
			$this->endPointer++;
		}
		
		return $lengthToRead;
	}
	
	public function reset() {
		if(!$this->isClosed()) {
			throw new Exception('ReadStream has been closed.');
		}
		
		$this->endPointer = $this->markPosition == -1 ? 0 : $this->markPosition;
	}
	
	public function read() {
		if($this->endPointer < $this->size) {
			$dataFromBufferHead = $this->streamData[$this->endPointer];
			$this->endPointer++;
		} else {
			$dataFromBufferHead = -1;
		}
		return $dataFromBufferHead;
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
	
	public function peek() {
		if($this->endPointer < $this->size) {
			$dataFromBufferHead = $this->streamData[$this->endPointer];
		} else {
			$dataFromBufferHead = -1;
		}
		return $dataFromBufferHead;
	}
	
	public function peekChar() {
		if($this->endPointer < $this->size) {
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
		
		$this->endPointer = $this->endPointer + $length;
		return $length;
	}
	
	public function atEnd() {
		return $this->endPointer == $this->size;
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