<?php

/**
 * read_stream.php contains class {@link ReadStream}.
 *
 * @author Jeff Stubler
 * @version 1.0
 * @package com.jeffstubler.streams;
 */

/**
 * {@code ReadStream} provides a sequential string buffer from which data can be read.
 * It is initialized with a string.
 *
 * @author Jeff Stubler
 * @version 1.0
 * @package com.jeffstubler.streams;
 */

class ReadStream {
	/**
	 * Internal data buffer.
	 */
	private $streamData;
	
	/**
	 * Index of last read index of the data buffer.
	 */
	private $endPointer;
	
	/**
	 * Total size of the data buffer.
	 */
	private $size;
	
	/**
	 * Mark position for reseting position.
		*/
	private $markPosition;
	
	/**
	 * Creates a new {@code ReadStream} object.
	 *
	 * @param mixed $data Data to initialize the stream to.
	 */
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
	
	/**
	 * Moves the reading position to the beginning or to the last marked position
	 * if one has been set.
	 */
	public function reset() {
		if(!$this->isClosed()) {
			throw new Exception('ReadStream has been closed.');
		}
		
		$this->endPointer = $this->markPosition == -1 ? 0 : $this->markPosition;
	}
	
	/**
	 * Reads the next character from the stream as an integer.
	 *
	 * @return integer Next character from the stream.
	 */
	public function read() {
		return $this->atEnd() ? -1 : $this->streamData[$this->endPointer++];
	}
	
	/**
	 * Reads characters as integers from the stream to an array.
	 *
	 * @param array Array to write integers from the stream to.
	 * @param integer $offset Initial position in the array to write to (Optional: defaults to 0).
	 * @param integer $length Number of characters to read from the stream to the array (Optional:
	 * defaults to array length after offset).
	 * @return integer Number of characters that were read to the array.
	 */
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
	
	/**
	 * Reads the next character from the stream.
	 *
	 * @return string Next character from the stream.
	 */
	public function readChar() {
		return $this->atEnd() ? '' : chr($this->streamData[$this->endPointer]);
	}
	
	/**
	 * Reads a string from the stream.
	 *
	 * @param integer $length Lenght of string to read from the stream (Optional: defaults to 1)
	 * @return string String read from the end of the stream.
	 */
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
	
	/**
	 * Reads characters from the stream to an array.
	 *
	 * @param array Array to write characters from the stream to.
	 * @param integer $offset Initial position in the array to write to (Optional: defaults to 0).
	 * @param integer $length Number of characters to read from the stream to the array (Optional:
	 * defaults to array length after offset).
	 * @return integer Number of characters that were read to the array.
	 */
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
	
	/**
	 * Reads the next character from the stream as an integer without advancing.
	 *
	 * @return integer Next character from the stream.
	 */
	public function peek() {
		return $this->atEnd() ? -1 : $this->streamData[$this->endPointer];
	}
	
	/**
	 * Reads the next character from the stream without advancing.
	 *
	 * @return string Next character from the stream.
	 */
	public function peekChar() {
		return $this->atEnd() ? -1 : chr($this->streamData[$this->endPointer]);
	}
	
	/**
	 * Closes the stream.
	 */
	public function close() {
		unsset($this->streamData);
	}
	
	/**
	 * Returns whether the stream has been closed.
	 *
	 * @return boolean True if the stream has been closed.
	 */
	public function isClosed() {
		return isset($this->streamData);
	}
	
	/**
	 * Moves the location to read from throughout the stream. Negative skip values are allowed.
	 * If the end of the stream has been reached, skipping is not allowed.
	 *
	 * @param integer $length Amount of characters to skip (Optional: defaults to 1).
	 * @return integer Length that was actually skipped.
	 */
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
	
	/**
	 * Returns whether the stream has reached its end.
	 *
	 * @return boolean True if the end of the stream has been reached.
	 */
	public function atEnd() {
		return $this->size == $this->endPointer;
	}
	
	/**
	 * Indicates a position where the stream can be reset to.
	 *
	 * @param integer $position Position of the mark.
	 */
	public function mark($position = 0) {
		if(!$this->isClosed()) {
			$this->markPosition = $position;
		} else {
			throw new Exception('ReadStream has been closed.');
		}
	}
	
	/**
	 * Return the stream as a string.
	 *
	 * @return string The contents of the buffer.
	 */
	public function __toString() {
		$stringRepresentation = '';
		
		for($currentIndex = 0; $currentIndex < $this->size; $currentIndex++) {
			$stringRepresentation .= chr($this->streamData[$currentIndex]);
		}
		
		return $stringRepresentation;
	}
}

?>