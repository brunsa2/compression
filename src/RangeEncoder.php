<?php

/**
 * RangeEncoder.php contains class {@link RangeEncoder}.
 *
 * @author Jeff Stubler
 * @version 1.0
 * @package com.jeffstubler.compression
 */

/**
 * {@code RangeEncoder} provides a range encoder for compressing probabalistic symbols
 * to a {@link WriteStream}.
 *
 * @author Jeff Stubler
 * @version 1.1
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
	private $bytesOutput = 0;
	
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
	 * Creates a new {@code RangeEncoder} with the proper constants to use either a 32- or 64-bit
	 * processor.
	 *
	 * @param boolean $forceThirtyTwoBitMath Force the range encoder to use 32-bit arithmetic.
	 */
	public function __construct(WriteStream $outputStream, $forceThirtyTwoBitMath = false) {
		$this->stream = $outputStream;
		
		$full = ($range = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x00ffffffffffffff : 0x00ffffff);
		$stable = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x0001000000000000 : 0x00010000;
		$maximumRange = PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 0x0000010000000000 : 0x00000100;
		$shiftDistance = $byteSize * (PHP_INT_SIZE == 4 || $forceThirtyTwoBitMath ? 6 : 2);
	}

}

?>