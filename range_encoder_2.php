<?php

class RangeEncoder2 {
	private $low = 0;
	private $range = 0xffffffff;
	
	private $flushed = false;
	
	private $stream;
	
	private $keepStatistics;
	private $statistics;
	private $currentByte;
	private $bytesOutput;
	
	const TOP = 0x1000000;
	const BOTTOM = 0x10000;
	
	public function __construct(WriteStream $stream, $keepStatistics = false) {
		$this->stream = $stream;
		$this->keepStatistics = $keepStatistics;
		$this->statistics = array();
		$this->currentByte = 0;
		$this->bytesOutput = 0;
	}
	
	public function reset() {
		$this->low = 0;
		$this->range = 0xffffffff;
	}
	
	public function encode($symbolLow, $symbolHigh, $frequencyRange) {
		if(!$this->flushed) {
			$this->range = (integer) ($this->range / $frequencyRange);
			$this->low += $symbolLow * $this->range;
			$this->range *= $symbolHigh - $symbolLow;
			
			echo 'Low: ' . dechex($this->low) . '; Range: ' . dechex($this->range) . '; High: ' . dechex($this->low + $this->range) . '<br />';
			
			if($this->keepStatistics) {
				$this->statistics[$this->currentByte] = $this->bytesOutput * 8 + $this->countStableBits(($this->low >> 24) & 0xff, (($this->low + $this->range) >> 24) & 0xff);
				$this->currentByte++;
			}
			
			while($this->firstByteStable() || $this->rangeUnderflow()) {
				if($this->rangeUnderflow() && !$this->firstByteStable()) {
					$this->range = (-$this->low & 0xffffffff) & (self::BOTTOM - 1);
				}
				
				echo 'Emit digit: ' . dechex($this->low >> 24) . '<br />';
				$this->stream->writeChar($this->low >> 24);
				
				if($this->keepStatistics) {
					$this->bytesOutput++;
				}
				
				$this->low = ($this->low << 8) & 0xffffffff;
				$this->range = ($this->range << 8) & 0xffffffff;
			}
		}
	}
	
	public function flush() {
		for($currentByte = 0; $currentByte < 4; $currentByte++) {
			echo 'Emit digit: ' . dechex($this->low >> 24) . '<br />';
			$this->stream->writeChar($this->low >> 24);
			$this->low = ($this->low << 8) & 0xffffffff;
			
			if($this->keepStatistics) {
				$this->bytesOutput++;
			}
		}
		
		if($this->keepStatistics) {
			$this->statistics[$this->currentByte] = $this->bytesOutput * 8;
		}
		
		$this->reset();
	}
	
	private function firstByteStable() {
		return (($this->low ^ ($this->low + $this->range)) & 0xffffffff) < self::TOP;
	}
	
	private function rangeUnderflow() {
		return ($this->range < self::BOTTOM);
	}
	
	public function getStream() {
		return $this->stream;
	}
	
	public function getStatistics() {
		return $this->statistics;
	}
	
	private function countStableBits($byteOne, $byteTwo) {
		$xorOfBytes = $byteOne ^ $byteTwo;
		
		if($xorOfBytes < 1) {
			return 8;
		} elseif($xorOfBytes < 2) {
			return 7;
		} elseif($xorOfBytes < 4) {
			return 6;
		} elseif($xorOfBytes < 8) {
			return 5;
		} elseif($xorOfBytes < 16) {
			return 4;
		} elseif($xorOfBytes < 32) {
			return 3;
		} elseif($xorOfBytes < 64) {
			return 2;
		} elseif($xorOfBytes < 128) {
			return 1;
		} else {
			return 6;
		}
	}
}

?>