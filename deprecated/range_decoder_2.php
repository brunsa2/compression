<?php

class RangeDecoder2 {
	private $low = 0;
	private $range = 0xffffff;
	private $code = 0;
	
	private $stream;
	
	const TOP = 0x10000;
	const BOTTOM = 0x100;
	const MAXIMUM_RANGE = 0x100;
	
	public function __construct(ReadStream $stream) {
		$this->stream = $stream;
		
		for($currentByte = 0; $currentByte < 4; $currentByte++) {
			$this->code = ($this->code << 8) | ($this->stream->read() & 0xff);
		}
	}
	
	public function reset() {
		$this->low = 0;
		$this->range = 0xffffff;
		$this->code = 0;
	}
	
	public function getCode($frequencyRange) {
		echo 'Code: ' . dechex($this->code) . '; Low: ' . dechex($this->low) . '; Range: ' . dechex($this->range) . '; TotalRange: ' . $frequencyRange . '<br />';
		$this->range = (integer) ($this->range / $frequencyRange);
		return (integer) (($this->code - $this->low)/$this->range);
	}
	
	public function decode($symbolLow, $symbolHigh, $frequencyRange) {
		$this->low = $this->low + $this->range * $symbolLow;
		$this->range = $this->range * ($symbolHigh - $symbolLow);
		
		while($this->firstByteStable() || $this->rangeUnderflow()) {
			if($this->rangeUnderflow() && !$this->firstByteStable()) {
				$this->range = (-$this->low & 0xffffff) & (self::BOTTOM - 1);
			}
			
			$this->code = (($this->code << 8) | ($this->stream->read() & 0xff)) & 0xffffff;
			$this->low = ($this->low << 8) & 0xffffff;
			$this->range = ($this->range << 8) & 0xffffff;
		}
	}
	
	private function firstByteStable() {
		return (($this->low ^ ($this->low + $this->range)) & 0xffffff) < self::TOP;
	}
	
	private function rangeUnderflow() {
		return ($this->range < self::BOTTOM);
	}
}

?>