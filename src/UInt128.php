<?php

class UInt128 {
	// Treat the number as base 256
	private $digits;
	
	public function __construct($number = 0, $radix = 10) {
		if(gettype($number) == 'integer' || gettype($number) == 'float') {
			$numebr = (integer) abs($number);
			for($currentByte = 0; $currentByte < 16; $currentByte++) {
				$this->digits[$currentByte] = $number & 0xff;
				$number >>= 8;
			}
		} else {
			$number = (string) $number;
			
			// Add automatic radix detecting code for stuff like 0x, also only takes radix 2, 8, 10, or 16
			// Convert intger cast to seperate methdod with switch block to remove issues like '2 ' becoming 20
			
			for($currentByte = 0; $currentByte < 16; $currentByte++) {
				$this->digits[$currentByte] = 0;
			}
			
			for($currentCharacter = 0; $currentCharacter < strlen($number); $currentCharacter++) {
				$currentNumber = (integer) hexdec(substr($number, $currentCharacter, 1));
				
				$this->multiply(new UInt128($radix));
				$this->add(new UInt128($currentNumber));
			}
		}
	}

	// this >>= shift
	// sets this = this >> shift
	// returns this
	public function shiftRight($shift) {
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
		
		return $this;
	}
	
	// this <<= shift
	// sets this = this << shift
	// returns this
	public function shiftLeft($shift) {
		for($currentShift = 0; $currentShift < $shift; $currentShift++) {
			$msbOfLowerByte = 0;
			$msbOfUpperByte = 0;
			
			for($currentByte = 0; $currentByte < 16; $currentByte++) {
				$this->digits[$currentByte] <<= 1;
				$msbOfUpperByte = $this->digits[$currentByte] & 0x100;
				$this->digits[$currentByte] &= 0xff;
				$this->digits[$currentByte] |= $msbOfLowerByte >> 8;
				$msbOfLowerByte = $msbOfUpperByte;
			}
		}
		
		return $this;
	}
	
	// negative if this is less than $number
	// this (*) number
	// (*) is <, <=, >, >=, ==, !=
	// for a (*) b
	// a.compareTo(b) (*) 0
	// use operator for what is wanted
	public function compareTo(UInt128 $number) {
		$comparison = 0;
		
		for($currentByte = 15; $currentByte >= 0; $currentByte--) {
			if($comparison == 0) {
				if($this->digits[$currentByte] < $number->digits[$currentByte]) {
					$comparison = -1;
				} else if($this->digits[$currentByte] > $number->digits[$currentByte]) {
					$comparison = 1;
				}
			} else {
				break;
			}
		}
		
		return $comparison;
	}
	
	// this += number
	// sets this = this + number
	// returns this
	public function add(UInt128 $number) {
		$carry = 0;
		
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] += $number->digits[$currentByte] + $carry;
			$carry = ($this->digits[$currentByte] & 0x100) >> 8;
			$this->digits[$currentByte] &= 0xff;
		}
		
		return $this;
	}
	
	// ++this
	// sets this = this + 1
	// returns this + 1
	public function preIncrement() {
		$this->digits[0]++;
		
		for($currentByte = 1; $currentByte < 16; $currentByte++) {
			if($this->digits[$currentByte - 1] > 255) {
				$this->digits[$currentByte - 1] = 0;
				$this->digits[$currentByte]++;
			} else {
				break;
			}
		}
		
		return $this;
	}
	
	// this++
	// sets this = this - 1
	// returns this
	public function postIncrement() {
		$preIncrementedValue = clone $this;
		
		$this->digits[0]++;
		
		for($currentByte = 1; $currentByte < 16; $currentByte++) {
			if($this->digits[$currentByte - 1] > 255) {
				$this->digits[$currentByte - 1] = 0;
				$this->digits[$currentByte]++;
			} else {
				break;
			}
		}
		
		return $preIncrementedValue;
	}
	
	// this -= number
	// sets this = this - number
	// returns this
	public function subtract(UInt128 $number) {
		$borrow = 0;
		
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] -= $number->digits[$currentByte] + $borrow;
			$borrow = $this->digits[$currentByte] < 0 ? 1 : 0;
			if($this->digits[$currentByte] < 0) {
				$this->digits[$currentByte] += 256;
			}
		}
		
		return $this;
	}
	
	// --this
	// sets this = this - 1
	// returns this - 1
	public function preDecrement() {
		$this->digits[0]--;
		
		for($currentByte = 1; $currentByte < 16; $currentByte++) {
			if($this->digits[$currentByte - 1] < 0) {
				$this->digits[$currentByte - 1] = 0;
				$this->digits[$currentByte]--;
			} else {
				break;
			}
		}
		
		return $this;
	}
	
	// this--
	// sets this = this - 1
	// returns this
	public function postDecrement() {
		$preIncrementedValue = clone $this;
		
		$this->digits[0]--;
		
		for($currentByte = 1; $currentByte < 16; $currentByte++) {
			if($this->digits[$currentByte - 1] < 0) {
				$this->digits[$currentByte - 1] = 0;
				$this->digits[$currentByte]--;
			} else {
				break;
			}
		}
		
		return $preIncrementedValue;
	}
	
	// this *= multiplicand
	// sets this = this * multiplicand
	// returns this
	public function multiply(UInt128 $multiplicand) {
		$product = new UInt128();
		$multiplicand = clone $multiplicand;
		$multiplier = clone $this;
		
		do {
			if(($multiplicand->digits[0] & 1) != 0) {
				$product->add($multiplier);
			}
			
			$multiplicand->shiftRight(1);
			$multiplier->shiftLeft(1);
		} while($multiplicand->compareTo(new UInt128(0)) != 0);
		
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] = $product->digits[$currentByte];
		}
		
		return $this;
	}
	
	// this /= divisor
	// sets this = this / divisor
	// returns this
	public function divide(UInt128 $divisor) {
		if($divisor->compareTo(new UInt128(0)) == 0) {
			throw new Exception('Division by zero');
		}
		
		$divisor = clone $divisor;
		$dividend = clone $this;
		$quotient = new UInt128();
		
		$shiftCount = 0;
		
		while($divisor->compareTo($dividend) < 0 && (($divisor->digits[15] & 0x80) == 0)) {
			$divisor->shiftLeft(1);
			$shiftCount++;
		}
		
		if($divisor->compareTo($dividend) > 0) {
			$divisor->shiftRight(1);
			$shiftCount--;
		}
		
		for($currentIteration = 0; $currentIteration <= $shiftCount; $currentIteration++) {
			if($divisor->compareTo($dividend) <= 0) {
				$dividend->subtract($divisor);
				$divisor->shiftRight(1);
				$quotient->shiftLeft(1);
				$quotient->postIncrement();
			} else {
				$divisor->shiftRight(1);
				$quotient->shiftLeft(1);
			}
		}
		
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] = $quotient->digits[$currentByte];
		}
		
		return $this;
	}
	
	// this %= divisor
	// sets this = this % divisor
	// returns this
	public function modulus(UInt128 $divisor) {
		if($divisor->compareTo(new UInt128(0)) == 0) {
			throw new Exception('Division by zero');
		}
		
		$divisor = clone $divisor;
		$dividend = clone $this;
		$quotient = new UInt128();
		
		$shiftCount = 0;
		
		while($divisor->compareTo($dividend) < 0 && (($divisor->digits[15] & 0x80) == 0)) {
			$divisor->shiftLeft(1);
			$shiftCount++;
		}
		
		if($divisor->compareTo($dividend) > 0) {
			$divisor->shiftRight(1);
			$shiftCount--;
		}
		
		for($currentIteration = 0; $currentIteration <= $shiftCount; $currentIteration++) {
			if($divisor->compareTo($dividend) <= 0) {
				$dividend->subtract($divisor);
				$divisor->shiftRight(1);
				$quotient->shiftLeft(1);
				$quotient->postIncrement();
			} else {
				$divisor->shiftRight(1);
				$quotient->shiftLeft(1);
			}
		}
		
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] = $dividend->digits[$currentByte];
		}
		
		return $this;
	}
	
	// this &= number
	// set this = this & number
	// returns this
	public function binaryAnd(UInt128 $number) {
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] &= $number->digits[$currentByte];
		}
		
		return $this;
	}
	
	// this |= number
	// set this = this | number
	// returns this
	public function binaryOr(UInt128 $number) {
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] |= $number->digits[$currentByte];
		}
		
		return $this;
	}
	
	// this ^= number
	// set this = this ^ number
	// returns this
	public function binaryXor(UInt128 $number) {
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] ^= $number->digits[$currentByte];
		}
		
		return $this;
	}
	
	public function get() {
		//return $this->digits[1] * 0x100 + $this->digits[0];
		$number = 0;
		
		for($currentByte = PHP_INT_SIZE == 4 ? 3 : 7; $currentByte >= 0; $currentByte--) {
			$number <<= 8;
			$number += $this->digits[$currentByte];
			
		}
		
		return $number;
	}
	
	public function getHexString() {
		$number = clone $this;
		$stringRepresentation = '';
		
		for($currentDigit = 7; $currentDigit >= 0; $currentDigit--) {
			
		}
	}
	
	public function power(UInt128 $exponent) {
		if($exponent->compareTo(new UInt128(0)) == 0) {
			for($currentDigit = 0; $currentDigit < 16; $currentDigit++) {
				$this->digits[$currentDigit] = $currentDigit == 0 ? 1 : 0;
			}
		} else {
			$base = clone $this;
			
			for($currentPower = 1; $exponent->compareTo(new UInt128($currentPower)) > 0; $currentPower++) {
				$this->multiply($base);
			}
		}
		
		return $this;
	}
	
	public function __toString() {
		$stringRepresentation = '';
		
		for($currentByte = 15; $currentByte >= 0; $currentByte--) {
			$stringRepresentation .= $this->digits[$currentByte] . ($currentByte == 0 ? '' : ' ');
		}
		
		return $stringRepresentation;
	}
}

?>