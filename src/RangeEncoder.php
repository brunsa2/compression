<?php

/**
 * RangeEncoder.php contains class {@link RangeEncoder}.
 *
 * @author Jeff Stubler
 * @version 2.0
 * @package com.jeffstubler.compression
 */

/**
 * {@code RangeEncoder} provides a range encoder for compressing probabalistic symbols
 * to a {@link WriteStream}.
 *
 * @author Jeff Stubler
 * @version 2.0
 * @package com.jeffstubler.compression
 */

class RangeEncoder {
	/**
	 * Low value of range.
	 */
	private $low = 0;
	
	/**
	 * Range width.
	 */
	private $range;
	
	/**
	 * Whether the encoder has been closed.
	 */
	private $closed = false;
	
	/**
	 * {@code WriteStream} where encoded data is written to.
	 */
	private $stream;
	
	/**
	 * Number of bytes output so far
	 */
	private $bytesOutputToStream = 0;
	
	/**
	 * Number of bits output so far
	 */
	private $bitsOutput = 0;
	
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
	 * Distance to shift most significant byte to least significant byte.
	 */
	private $shiftDistance;
	
	/**
	 * Number of bytes to output when closing the stream.
	 */
	private $closeByteOutput;
	
	/**
	 * Creates a new {@code RangeEncoder} with the proper constants to use either a 32- or 64-bit
	 * processor.
	 *
	 * @param boolean $forceThirtyTwoBitMath Force the range encoder to use 32-bit arithmetic.
	 */
	public function __construct(WriteStream $outputStream, $forceThirtyTwoBitMath = false) {
		$this->stream = $outputStream;
		
		$this->full = ($this->range = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x00ffffff : 0x00ffffffffffffff);
		$this->stable = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x00010000 : 0x0001000000000000;
		$this->maximumRange = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x00000100 : 0x0000010000000000;
		$this->shiftDistance = $this->byteSize * (PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 2 : 6);
		$this->closeByteOutput = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 3 : 7;
		/*
		$this->full = $this->range = 0xffffffff;
		$this->stable = 0x1000000;
		$this->maximumRange = 0x10000;
		$this->shiftDistance = 24;
		$this->closeByteOutput = 4;
		*/
	}
	
	/**
	 * Encodes a symbol to the output stream.
	 *
	 * @param integer $low Low cumulative frequency for the encoded symbol.
	 * @param integer $high High cumulative frequency for the encoded symbol.
	 * @param integer $range Total frequency range for the encoded symbol.
	 * @return integer The number of bits used to encode the symbol.
	 */
	public function encodeSymbol($low, $high, $range) {
		if($this->isClosed()) {
			throw new Exception('Range encoder has been closed');
		} else {
			// Main portion of range encoding is here: adjust the low value and range based on the
			// symbol to encode.
			$this->range = (integer) ($this->range / $range);
			$this->low += $low * $this->range;
			$this->range *= $high - $low;
			
			// Check for range underflows or digits to emit.
			while($this->firstByteIsStable() || $this->rangeUnderflow()) {
				// Correct an underflow by expanding the range.
				$this->range = (!$this->firstByteIsStable() && $this->rangeUnderflow()) ? ((-$this->low & $this->full) & ($this->maximumRange - 1)) : $this->range;
				
				$this->stream->writeInt($this->low >> $this->shiftDistance);
								
				$this->low <<= $this->byteSize;
				$this->low &= $this->full;
				
				$this->range <<= $this->byteSize;
				$this->range &= $this->full;
				
				$this->bytesOutputToStream++;
			}
			
			for($xorOfLowAndHigh = ($this->low >> $this->shiftDistance) & 0xff ^ (($this->low + $this->range) >> $this->shiftDistance) & 0xff, $stableBits = 8; $xorOfLowAndHigh >= pow(2, 8 - $stableBits); $stableBits--);
			$bitsCurrentlyOutput = $this->bytesOutputToStream * $this->byteSize + $stableBits;
			
			$bitsEncodedForCurrentSymbol = $bitsCurrentlyOutput - $this->bitsOutput;
			$this->bitsOutput = $bitsCurrentlyOutput;
			return $bitsEncodedForCurrentSymbol;
		}
	}
	
	// TODO: Commented lines in the following method must be tested with the decoder: they are
	// to block out excessive low value bytes from being written if they are unnecessary
	// If the decoder works with them, they can be uncommented.
	
	/**
	 * Closes the encoder and emits the last bytes to the stream.
	 *
	 * @return integer The number of bits in the entire coded message.
	 */
	public function close() {
		if($this->isClosed()) {
			throw new Exception('Range encoder has been closed');
		} else {
			for($currentByte = 0; $currentByte < $this->closeByteOutput; $currentByte++) {
				if($this->range > 0) {
					$this->stream->writeInt($this->low >> $this->shiftDistance);
					$this->bytesOutputToStream++;
				} else {
				break;
				}
				
				$this->low <<= $this->byteSize;
				$this->low &= $this->full;
				
				$this->range <<= $this->byteSize;
				$this->range &= $this->full;				
			}
		}
		
		return ($this->bytesOutputToStream * $this->byteSize) - $this->bitsOutput;
	}
	
	/**
	 * Returns the {@link WriteStream} object that data is being encoded to.
	 *
	 * @return WriteStream Output stream.
	 */
	public function getStream() {
		return $this->stream;
	}
	
	/**
	 * Returns a string of the encoded data.
	 *
	 * @return string Output string.
	 */
	public function __toString() {
		return (string) $this->stream;
	}
	
	/**
	 * Return the encoders's maximum range.
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
	
	/**
	 * Returns whether the encoder is closed.
	 *
	 * @return boolean True if the encoder is closed.
	 */
	public function isClosed() {
		return $this->closed;
	}

}

?>