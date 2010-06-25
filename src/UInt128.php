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
			
			if(substr($number, 0, 1) == '0') {
				$number = substr($number, 1);
				
				if(substr($number, 0, 1) == 'x') {
					$radix = 16;
					$number = substr($number, 1);
				} else {
					$radix = 8;
				}
			}
			
			if(!($radix == 2 || $radix == 8 || $radix == 10 || $radix == 16)) {
				throw new Exception('Invalid radix');
			}
			
			for($currentByte = 0; $currentByte < 16; $currentByte++) {
				$this->digits[$currentByte] = 0;
			}
			
			for($currentCharacter = 0; $currentCharacter < strlen($number); $currentCharacter++) {
				$currentNumber = self::getNumberFromDigit(substr($number, $currentCharacter, 1), $radix);
				
				$this->multiply(new UInt128($radix));
				$this->add(new UInt128($currentNumber));
			}
		}
	}
	
	private function getNumberFromDigit($digit, $radix) {
		switch($digit) {
			case '0': return 0;
			case '1': return 1;
			case '2': if(!($radix == 8 || $radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 2;
			case '3': if(!($radix == 8 || $radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 3;
			case '4': if(!($radix == 8 || $radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 4;
			case '5': if(!($radix == 8 || $radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 5;
			case '6': if(!($radix == 8 || $radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 6;
			case '7': if(!($radix == 8 || $radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 7;
			case '8': if(!($radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 8;
			case '9': if(!($radix == 10 || $radix == 16)) { throw new Exception('Invalid digit in number'); } return 9;
			case 'A': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 10;
			case 'a': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 10;
			case 'B': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 11;
			case 'b': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 11;
			case 'C': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 12;
			case 'c': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 12;
			case 'D': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 13;
			case 'd': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 13;
			case 'E': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 14;
			case 'e': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 14;
			case 'F': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 15;
			case 'f': if(!($radix == 16)) { throw new Exception('Invalid digit in number'); } return 15;
			default:
				throw new Exception('Invalid digit in number');
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
	public function compareTo($number) {
		$comparison = 0;
		
		if(is_integer($number) || is_string($number)) {
			$number = new UInt128($number);
		}
		
		if(get_class($number) != 'UInt128') {
			throw new Exception('Invalid number');
		}
		
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
	public function add($number) {
		$carry = 0;
		
		if(is_integer($number) || is_string($number)) {
			$number = new UInt128($number);
		}
		
		if(get_class($number) != 'UInt128') {
			throw new Exception('Invalid number');
		}
		
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
	public function subtract($number) {
		$borrow = 0;
		
		if(is_integer($number) || is_string($number)) {
			$number = new UInt128($number);
		}
		
		if(get_class($number) != 'UInt128') {
			throw new Exception('Invalid number');
		}
		
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
	public function divideSelf(UInt128 $divisor) {
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
	
	// returns dividend / divisor
	public static function divide(UInt128 $dividend, UInt128 $divisor) {
		$dividend = clone $dividend;
		$dividend->divideSelf($divisor);
		return $dividend;
	}
	
	// this %= divisor
	// sets this = this % divisor
	// returns this
	public function modulusSelf(UInt128 $divisor) {
		if($divisor->compareTo(new UInt128(0)) == 0) {
			throw new Exception('Division by zero');
		}
		
		$divisor = clone $divisor;
		$dividend = clone $this;
		
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
			} else {
				$divisor->shiftRight(1);
			}
		}
		
		for($currentByte = 0; $currentByte < 16; $currentByte++) {
			$this->digits[$currentByte] = $dividend->digits[$currentByte];
		}
		
		return $this;
	}
	
	// returns dividend % divisor
	public static function modulus(UInt128 $dividend, UInt128 $divisor) {
		$dividend = clone $dividend;
		$dividend->modulusSelf($divisor);
		return $dividend;
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
	
	public function getString($radix = 10) {
		if(!($radix == 2 || $radix == 8 || $radix == 10 || $radix == 16)) {
			throw new Exception('Invalid radix');
		}
		
		$numberOfDigits = 0;
		
		switch($radix) {
			case 2:
				$numberOfDigits = 127;
				break;
			case 8:
				$numberOfDigits = 42;
				break;
			case 10:
				$numberOfDigits = 38;
				break;
			case 16:
				$numberOfDigits = 31;
		}
		
		$number = clone $this;
		$radix = new UInt128($radix);
		$stringRepresentation = '';
		$emitZeroes = false;
		
		for($currentDigit = $numberOfDigits; $currentDigit >= 0; $currentDigit--) {
			$nextDigit = UInt128::divide($number, self::power($radix, new UInt128($currentDigit)));
			$number = UInt128::modulus($number, self::power($radix, new UInt128($currentDigit)));
			
			if(($nextDigit->digits[0] == 0 && $emitZeroes) || $nextDigit->digits[0] != 0) {
				$stringRepresentation .= dechex($nextDigit->digits[0]);
				$emitZeroes = true;
			}
		}
		
		return $stringRepresentation;
	}
	
	public function powerSelf(UInt128 $exponent) {
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
	
	public static function power(UInt128 $base, UInt128 $exponent) {
		$base = clone $base;
		$base->powerSelf($exponent);
		return $base;
	}
	
	public function __toString() {
		return $this->getString(10);
	}
}

?>