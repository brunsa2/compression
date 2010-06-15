<?php

class CanonicalHuffmanTree {
	private $symbols = array();
	private $size;
	
	public function createSymbolNode($data, $weight) {
		$this->symbols[0]['Data'] = $data;
		$this->symbols[0]['Weight'] = $weight;
		$this->size = 1;
	}
	
	public function mergeTrees($left, $right) {
		$this->size = 0;
		
		for($currentIndex = 0; $currentIndex < count($left); $currentIndex++) {
			$this->symbols[$this->size]['Data'] = $left->symbols[$currentIndex]['Data'];
			$this->symbols[$this->size]['Weight'] = $left->symbols[$currentIndex]['Weight'];
			$this->size++;
		}
		
		for($currentIndex = 0; $currentIndex < count($right); $currentIndex++) {
			$this->symbols[$this->size]['Data'] = $right->symbols[$currentIndex]['Data'];
			$this->symbols[$this->size]['Weight'] = $right->symbols[$currentIndex]['Weight'];
			$this->size++;
		}
	}
	
	public function getNodeSymbol() {
		return $this->symbols[0]['Data'];
	}
	
	public function getNodeWeight() {
		$totalWeight = 0;
		for($currentIndex = 0; $currentIndex < count($this->symbols); $currentIndex++) {
			$totalWeight += $this->symbols[$currentIndex]['Weight'];
		}
		
		return $totalWeight;
	}
	
	public function __toString() {
		return $this->data;
	}
}

?>