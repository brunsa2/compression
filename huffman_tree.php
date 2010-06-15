<?php

class HuffmanTree {
	private $left, $right;
	private $data;
	private $weight;
	
	public function __construct() {
		$root = null;
	}
	
	public function createSymbolNode($data, $weight) {
		$this->left = null;
		$this->right = null;
		$this->weight = $weight;
		$this->data = $data;
	}
	
	public function mergeTrees($left, $right) {
		$this->left = $left;
		$this->right = $right;
		$this->data = null;
		print_r($left);
		print_r($right);
		$this->weight = $left->weight + $right->weight;
	}
	
	public function getNodeSymbol() {
		return $this->data;
	}
	
	public function getNodeWeight() {
		return $this->weight;
	}
	
	public function __toString() {
		return $this->data;
	}
	
	public function traverse() {
		if($this->left != null) {
			$this->left->traverse();
		}
		
		if($this->data != null) {
			echo (string) $this->weight . ' ' . $this->data . '<br />';
		}
		
		if($this->right != null) {
			$this->right->traverse();
		}
	}
}

?>