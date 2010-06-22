<?php

class UInt128 {
	private $digits;
	
	public function __construct() {
		
		
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] = 0;
		}
	}
	
	public function set($number) {
		$this->digits[0] = $number & 0xff;
		$this->digits[1] = ($number & 0xff00) >> 8;
	}
	
	public function shiftLeft($shift) {
		for($currentShift = 0; $currentShift < $shift; $currentShift++) {
			$lsbOfUpperByte = 0;
			$lsbOfLowerByte = 0;
			
			for($currentByte = 15; $currentByte >= 0; $currentByte--) {
				$lsbOfLowerByte = $this->digits[$currentByte] & 0x01;
				$this->digits[$currentByte] >>= 1;
				$this->digits[$currentByte] |= $lsbOfUpperByte << 7;
				$lsbOfUpperByte = $lsbOfLowerByte;
			}
		}
	}
	
	public function shiftRight($shift) {
		for($currentShift = 0; $currentShift < $shift; $currentShift++) {
			$msbOfLowerByte = 0;
			$msbOfUpperByte = 0;
			
			for($currentByte = 0; $currentByte < 16; $currentByte++) {
				$msbOfUpperByte = $this->digits[$currentByte] & 0x80;
				$this->digits[$currentByte] <<= 1;
				$this->digits[$currentByte] |= $msbOfLowerByte >> 7;
				$msbOfLowerByte = $msbOfUpperByte;
			}
		}
	}
	
	public function get() {
		return $this->digits[1] * 0x100 + $this->digits[0];
	}
}

?>