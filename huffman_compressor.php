<?php

class HuffmanCompressor {
	
	public function __construct($uncompressedString) {
		$fullFrequencies = $this->frequencyAnalysis($uncompressedString);
		
		$frequencies = array();
		$symbols = array();
		
		for($currentCharacter = 0; $currentCharacter < 256; $currentCharacter++) {
			if($fullFrequencies[$currentCharacter] > 0) {
				echo chr($currentCharacter) . ': ' . $fullFrequencies[$currentCharacter] . '<br />';
				
				$symbolTree[0]['Data'] = chr($currentCharacter);
				$symbolTree[0]['Weight'] = $fullFrequencies[$currentCharacter];
				$symbolTree[0]['Level'] = 0;
				
				$frequencies[$currentCharacter] = $fullFrequencies[$currentCharacter];
				$symbols[$currentCharacter] = $symbolTree;
			}
		}
		
		echo '<br />';
		
		array_multisort($frequencies, $symbols);
		/*
		print_r($frequencies);
		print_r($symbols);
		*/
		$symbolQueue = new Queue();
		$treeQueue = new Queue();
		
		for($currentTree = 0; $currentTree < count($symbols); $currentTree++) {
			$symbolQueue->enqueue($symbols[$currentTree]);
		}
		
		while($symbolQueue->size() + $treeQueue->size() > 1) {
			$topOfSymbolQueue = null;
			$topOfTreeQueue = null;
			$symbolQueueWeight = 0;
			$treeQueueWeight = 0;
			
			if($symbolQueue->peek() != null) {
				$topOfSymbolQueue = $symbolQueue->peek();
				$symbolQueueWeight = 0;
				
				for($currentIndex = 0; $currentIndex < count($topOfSymbolQueue); $currentIndex++) {
					$symbolQueueWeight += $topOfSymbolQueue[$currentIndex]['Weight'];
				}
			}
			
			if($treeQueue->peek() != null) {
				$topOfTreeQueue = $treeQueue->peek();
				$treeQueueWeight = 0;
				
				for($currentIndex = 0; $currentIndex < count($topOfTreeQueue); $currentIndex++) {
					$treeQueueWeight += $topOfTreeQueue[$currentIndex]['Weight'];
				}
			}
			
			if($treeQueueWeight >= $symbolQueueWeight) {
				$leftTree = $treeQueue->dequeue();
			} else {
				$leftTree = $symbolQueue->dequeue();
			}
			
			//echo $topOfSymbolQueue . ' ' . $topOfTreeQueue . '<br />';
			
			if($symbolQueue->peek() != null) {
				$topOfSymbolQueue = $symbolQueue->peek();
				$symbolQueueWeight = 0;
				
				for($currentIndex = 0; $currentIndex < count($topOfSymbolQueue); $currentIndex++) {
					$symbolQueueWeight += $topOfSymbolQueue[$currentIndex]['Weight'];
				}
			}
			
			if($treeQueue->peek() != null) {
				$topOfTreeQueue = $treeQueue->peek();
				$treeQueueWeight = 0;
				
				for($currentIndex = 0; $currentIndex < count($topOfTreeQueue); $currentIndex++) {
					$treeQueueWeight += $topOfTreeQueue[$currentIndex]['Weight'];
				}
			}
			
			if($treeQueueWeight >= $symbolQueueWeight) {
				$rightTree = $treeQueue->dequeue();
			} else {
				$rightTree = $symbolQueue->dequeue();
			}
			
			//echo $topOfSymbolQueue . ' ' . $topOfTreeQueue . '<br />';
			
			print_r($leftTree);
			echo '<br />';
			print_r($rightTree);
			echo '<br />';
			
			//$mergedTree = new CanonicalHuffmanTree();
			//$mergedTree->mergeTrees($leftTree, $rightTree);
			
			$mergedTree = array_merge($leftTree, $rightTree);
			
			for($currentIndex = 0; $currentIndex < count($mergedTree); $currentIndex++) {
				$mergedTree[$currentIndex]['Level']++;
			}
			
			$treeQueue->enqueue($mergedTree);
		}
		
		print_r($treeQueue->peek());
		
		while($symbolQueue->isFull()) {
			$symbol = $symbolQueue->dequeue();
			echo $symbol->getNodeSymbol() . ': ' . $symbol->getNodeWeight() . '<br />';
		}
	}
	
	private function frequencyAnalysis($uncompressedString) {
		for($currentIndex = 0; $currentIndex < 256; $currentIndex++) {
			$frequencyArray[$currentIndex] = 0;
		}
		
		for($currentCharacter = 0; $currentCharacter < strlen($uncompressedString); $currentCharacter++) {
			$frequencyArray[ord(substr($uncompressedString, $currentCharacter, 1))]++;
		}
		
		return $frequencyArray;
	}
}

?>