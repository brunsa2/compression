<?php

class RangeEncoder {
	const INITIAL_RANGE_64_TOP = 0x00ffffff;
	const INITIAL_RANGE_64_BOTTOM = 0xffffffff;
	const INITIAL_RANGE_32 = 0x00ffffff;
	const INITIAL_RANGE_10 = 100000;
	
	const INITIAL_LOW_64_TOP = 0x00000000;
	const INITIAL_LOW_64_BOTTOM = 0x00000000;
	const INITIAL_LOW_32 = 0x00000000;
	const INITIAL_LOW_10 = 0;
	
	const BYTE_SIZE = 8;
	const LONG_SHIFT = 32;
	
	const BYTES = PHP_INT_SIZE;
	const FORCE_32_BIT_MATH = 0;
	const FORCE_DECIMAL_MATH = 1;
	
	private $low;
	private $range;
	private $high = 99999;
	
	private $stream;
	
	public function __construct($stream) {
		if(self::BYTES * self::BYTE_SIZE == 32 || self::FORCE_32_BIT_MATH) {
			$this->low = self::INITIAL_LOW_32;
			$this->range = self::INITIAL_RANGE_32;
		} elseif(self::FORCE_DECIMAL_MATH) {
			$this->low = self::INITIAL_LOW_10;
			$this->range = self::INITIAL_RANGE_10;
		} else {
			$this->low = (self::INITIAL_LOW_64_TOP << self::LONG_SHIFT) + self::INITIAL_LOW_64_BOTTOM;
			$this->range = (self::INITIAL_RANGE_64_TOP << self::LONG_SHIFT) + self::INITIAL_RANGE_64_BOTTOM;
		}
		
		$this->stream = $stream;
		
		echo 'Low: ' . (self::FORCE_DECIMAL_MATH ? $this->low : dechex($this->low)) . '; High: ' .
				(self::FORCE_DECIMAL_MATH ? $this->high : dechex($this->high)) . '<br />';
	}
	
	public function encode($symbolLow, $symbolHigh, $totalRange) {
		$scaledRange = ($this->high - $this->low) / $totalRange;
		
		$this->high = (integer) ($this->low + $scaledRange * $symbolHigh);
		$this->low = (integer) ($this->low + $scaledRange * $symbolLow);
		
		if($this->high - $this->low < 1000) {
			echo 'Emitting ' . (integer) ($this->low / 10000) . '<br />';
			$this->stream->write((integer) ($this->low / 10000));
			$this->low = ($this->low % 10000) * 10;
			echo 'Emitting ' . (integer) ($this->low / 10000) . '<br />';
			$this->stream->write((integer) ($this->low / 10000));
			$this->low = ($this->low % 10000) * 10;
			$this->high = 99999;
		}
		
		while((integer) ($this->low / 10000) == (integer) ($this->high / 10000)) {
			echo 'Emitting ' . (integer) ($this->low / 10000) . '<br />';
			$this->stream->write((integer) ($this->low / 10000));
			$this->low = ($this->low % 10000) * 10;
			$this->high = ($this->high % 10000) * 10;
		}
		
		//echo '<br />Low int: ' . (integer) ($this->low / 10000) . '; High int: ' . (integer) $this->high / 10000 . '<br />';
		
		echo 'Low: ' . (self::FORCE_DECIMAL_MATH ? $this->low : dechex($this->low)) . '; High: ' .
				(self::FORCE_DECIMAL_MATH ? $this->high : dechex($this->high)) . '<br />';
	}
	
	public function flush() {
		while($this->high - $this->low < 10000) {
			echo 'Emitting ' . (integer) ($this->low / 10000) . '<br />';
			$this->stream->write((integer) ($this->low / 10000));
			$this->low = ($this->low % 10000) * 10;
			$this->low = $this->low + ($this->high - $this->low) * 10;
		}
		
		$this->low = $this->low + 10000;
		echo 'Emitting ' . (integer) ($this->low / 10000) . '<br />';
		$this->stream->write((integer) ($this->low / 10000));
			$this->low = ($this->low % 10000) * 10;
	}
	
}

?>