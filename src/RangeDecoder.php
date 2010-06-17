<?php

/**
 * RangeDecoder.php contains class {@link RangeDecoder}.
 *
 * @author Jeff Stubler
 * @version 2.0
 * @package com.jeffstubler.compression
 */

/**
 * {@code RangeDecoder} provides a range decoder for decompression probabalistic symbols
 * from a {@link ReadStream}.
 *
 * @author Jeff Stubler
 * @version 2.0
 * @package com.jeffstubler.compression
 */

class RangeDecoder {
	/**
	 * Low value of range.
	 */
	private $low = 0;
	
	/**
	 * Range width.
	 */
	private $range;
	
	/**
	 * Current code.
	 */
	private $code = 0;
	
	/**
	 * {@code ReadStream} where data is read from.
	 */
	private $stream;
	
	/**
	 * Full number with all bits set
	 */
	private $full;
	
	/**
	 * Comparison for stable byte
	 */
	private $stable;
	
	/**
	 * Maxmimum range
	 */
	private $maximumRange;
	
	/**
	 * Number of bites in a byte
	 */
	private $byteSize = 8;
	
	/**
	 * Number of bytes to input when opening the stream.
	 */
	private $openByteInput;
	
	/**
	 * Creates a new {@code RangeDecoder} with the proper constants to use either a 32- or 64-bit
	 * processor.
	 *
	 * @param boolean $forceThirtyTwoBitMath Force the range decoder to use 32-bit arithmetic.
	 */
	public function __construct(ReadStream $inputStream, $forceThirtyTwoBitMath = false) {
		$this->stream = $inputStream;
		
		$this->full = ($this->range = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x00ffffff : 0x00ffffffffffffff);
		$this->stable = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x00010000 : 0x0001000000000000;
		$this->maximumRange = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x00000100 : 0x0000010000000000;
		$this->openByteInput = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 4 : 8;
		
		for($currentByte = 0; $currentByte < $this->openByteInput; $currentByte++) {
			$this->code = ($this->code << $this->byteSize) | $this->stream->read();
		}
	}
	
	/**
	 * Returns the current frequency encoded given the total frequency range.
	 *
	 * @param integer $range Total frequency range used to encode the current symbol.
	 * @return integer Current symbol frequency encoded in stream.
	 */
	
	public function getFrequency($range) {
		// Adjust decoder range to the frequency range to get the current symbol frequency
		$this->range = (integer) ($this->range / $range);
		return (integer) (($this->code - $this->low) / $this->range);
	}
	
	/**
	 * Removes the current encoded frequency range from the stream.
	 *
	 * @param integer $low Low cumulative frequency for the encoded symbol.
	 * @param integer $high High cumulative frequency for the encoded symbol.
	 * @param integer $range Total frequency range for the encoded symbol.
	 */
	
	public function removeRange($low, $high, $range) {
		// Adjust the decoder low and range to remove the current symbol frequency to go to the next.
		$this->low += $this->range * $low;
		$this->range *= $high - $low;
		
		while($this->firstByteIsStable() || $this->rangeUnderflow()) {
			// Correct an underflow by expanding the range.
			$this->range = (!$this->firstByteIsStable() && $this->rangeUnderflow()) ? ((-$this->low & $this->full) & ($this->maximumRange - 1)) : $this->range;
			
			if($this->stream->atEnd()) {
				throw new Exception('Range decoder has run out of data');
			}
			
			$this->code <<= $this->byteSize;
			$this->code |= $this->stream->read();
			$this->code &= $this->full;
			
			$this->low <<= $this->byteSize;
			$this->low &= $this->full;
			
			$this->range <<= $this->byteSize;
			$this->range &= $this->full;
		}
	}
	
	/**
	 * Return the decoder's maximum range.
	 *
	 * @return integer Maximum range.
	 */
	public function getMaximumRange() {
		return $this->maximumRange;
	}
	
	/**
	 * Returns whether the most significant byte of the low and high values of the range is stable
	 * allowing for a byte to be emitted to the stream.
	 *
	 * @return boolean True if the most significant byte is stable.
	 */
	private function firstByteIsStable() {
		return (($this->low ^ ($this->low + $this->range)) & $this->full) < $this->stable;
	}
	
	/**
	 * Returns whether the range has underflowed and adjustments need to be made.
	 *
	 * @return boolean True if the range has underflowed.
	 */
	private function rangeUnderflow() {
		return $this->range < $this->maximumRange;
	}
}

?>