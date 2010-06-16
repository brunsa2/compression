<?php

/**
 * write_stream.php contains class {@link WriteStream}
 *
 * @author Jeff Stubler
 * @version 1.0
 * @package com.jeffstubler.streams;
 */

/**
 * {@code WriteStream} provides a sequential buffer in which different data types can be
 * appended. The entire buffer can be accessed as a string.
 *
 * @author Jeff Stubler
 * @version 1.0
 * @package com.jeffstubler.streams;
 */

class WriteStream {
	/**
	 * Internal data buffer.
	 */
	protected $streamData;
	
	/**
	 * Index of last item in buffer.
	 */
	protected $endPointer;
	
	/**
	 * Creates a new WriteStream object.
	 */
	public function __construct() {
		$this->streamData = array();
		$this->endPointer = 0;
	}
	
	/**
	 * Write a number to the stream. Only the least significant byte is written.
	 * 
	 * @param mixed $data Number to write to the stream.
	 */
	public function writeInt($data = 0) {
		$data = (integer) $data & 0xff;
		
		$this->streamData[$this->endPointer++] = $data;
	}
	
	/**
	 * Write data to the stream. Data is written as a string.
	 *
	 * @param mixed $data Data to write to the stream;
	 * @param integer $offset Starting position in 
	 */
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
					$this->streamData[$this->endPointer++] = ord(substr($stringRepresentation, $currentCharacter, 1));
				}
			}
		} elseif(gettype($data) == 'string') {
			if($length == null) {
				$length = strlen($data) - $offset;
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