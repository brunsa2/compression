<?php

class BitstreamWriter {
	private $bytes;
	private $currentByteArrayIndex;
	private $buffer;
	private $freeBufferBits;
	private $open = false;
	
	public function __construct() {
		$this->currentByteArrayIndex = 0;
		$this->buffer = 0;
		$this->freeBufferBits = 8;
		$this->open = true;
	}
	
	public function writeCode($code, $codeLength) {
		if($this->open) {
			while($codeLength > 0) {
				$frontBits = $this->getFrontBits($code, min($this->freeBufferBits, $codeLength), $codeLength);
				$backBits = $this->getBackBits($code, min($this->freeBufferBits, $codeLength), $codeLength);
				
				//echo 'Front bits: ' . decbin((string) $frontBits) . '; Back bits: ' . decbin((string) $backBits) . '<br />';
				
				$this->buffer = $this->packBits($this->buffer, $frontBits, min($this->freeBufferBits, $codeLength));
				$newCodeLength = $codeLength - min($this->freeBufferBits, $codeLength);
				//echo 'Free buffer bits before: ' . (string) $this->freeBufferBits . '; Code length before: ' . (string) $codeLength . '<br />';
				$this->freeBufferBits = $this->freeBufferBits - min($this->freeBufferBits, $codeLength);
				//echo 'Free buffer bits after: ' . (string) $this->freeBufferBits . '; Code length after: ' . (string) $newCodeLength . '<br />';
				$codeLength = $newCodeLength;
				$code = $backBits;
				
				if($this->freeBufferBits <= 0) {
					$this->bytes[$this->currentByteArrayIndex] = $this->buffer;
					$this->currentByteArrayIndex++;
					$this->freeBufferBits = 8;
					$this->buffer = 0;
				}
				
				//echo 'Buffer: ' . decbin((string) $this->buffer) . '<br />';
				//echo 'Output: ' . dechex((string) $this->bytes[0]) . ' ' . dechex((string) $this->bytes[1]) . ' ' . dechex((string) $this->bytes[2]) . ' ' . dechex((string) $this->bytes[3]) . ' ' . '<br /><br />';
			}
		}
	}
	
	private function getFrontBits($code, $frontBits, $totalBits) {
		return $code >> ($totalBits - $frontBits);
	}
	
	private function getBackBits($code, $frontBits, $totalBits) {
		return $code - ($this->getFrontBits($code, $frontBits, $totalBits) << ($totalBits - $frontBits));
	}
	
	private function packBits($buffer, $code, $codeLength) {
		return ($buffer << $codeLength) + $code;
	}
	
	public function close() {
		//echo 'Current buffer: ' . decbin((string) $this->buffer) . '<br />';
		$this->buffer = $this->packBits($this->buffer, 0x00, $this->freeBufferBits);
		//echo 'Current buffer after packing: ' . decbin((string) $this->buffer) . '<br />';
		//echo 'Placed at address: ' . (string) $this->currentByteArrayIndex . '<br />';
		$this->bytes[$this->currentByteArrayIndex] = $this->buffer;
		$this->currentByteArrayIndex++;
		$this->freeBufferBits = 8;
		$this->buffer = 0;
		//echo 'Output: ' . dechex((string) $this->bytes[0]) . ' ' . dechex((string) $this->bytes[1]) . ' ' . dechex((string) $this->bytes[2]) . ' ' . dechex((string) $this->bytes[3]) . ' ' . '<br /><br />';
		$this->open = false;
	}
	
	public function __toString() {
		$stringRepresentation = '';
		for($currentByte = 0; $currentByte < $this->currentByteArrayIndex; $currentByte++) {
			$stringRepresentation .= chr($this->bytes[$currentByte]);
		}
		
		return $stringRepresentation . ' ' . strlen($stringRepresentation);
	}
	
	
}

?>