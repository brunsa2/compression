<?php

class PriorityQueue {
	private $queueItems;
	private $size;
	
	public function __construct() {
		$this->size = 0;
	}
	
	private function leftChildNode($nodeIndex) {
		return $nodeIndex * 2 + 1;
	}
	
	private function rightChildNode($nodeIndex) {
		return $nodeIndex * 2 + 2;
	}
	
	private function parentNode($nodeIndex) {
		return floor(($nodeIndex - 1) / 2);
	}
	
	public function size() {
		return $this->size;
	}
	
	public function enqueue($priority, $data) {
		$this->queueItems[$this->size]['Priority'] = $priority;
		$this->queueItems[$this->size]['Data'] = $data;
		
		$childNode = $this->size;
		$parentNode = $this->parentNode($childNode);
		//echo 'Child node: ' . (string) $childNode . '; Parent node: ' . (string) $parentNode . '<br />';
		
		while($parentNode != -1) {
			if($this->queueItems[$childNode]['Priority'] < $this->queueItems[$parentNode]['Priority']) {
				
				$this->swapArrayElements($parentNode, $childNode);
				
				$childNode = $parentNode;
				$parentNode = $this->parentNode($childNode);
			} else {
				break;
			}
		}
		
		//echo 'Priority: ' . $this->queueItems[$this->size]['Priority'] .
		 //    '; Data: ' . $this->queueItems[$this->size]['Data'] . '<br />';
		
		$this->size++;
	}
	
	public function dequeue() {
		if($this->size > 0) {
			$dequeueData = $this->queueItems[0]['Data'];
			
			$this->queueItems[0]['Priority'] = $this->queueItems[$this->size - 1]['Priority'];
			$this->queueItems[0]['Data'] = $this->queueItems[$this->size - 1]['Data'];
			
			unset($this->queueItems[$this->size - 1]);
			$this->size--;
			
			$this->minHeapify(0);
			
			//echo $dequeueData . '<br />';
			
			return $dequeueData;
		} else {
			return null;
		}
	}
	
	private function minHeapify($node) {
		$leftChildNode = $this->leftChildNode($node);
		$rightChildNode = $this->rightChildNode($node);
		
		$largestNode = $node;
		
		if($leftChildNode < $this->size && $this->queueItems[$leftChildNode]['Priority'] < $this->queueItems[$node]['Priority']) {
			$largestNode = $leftChildNode;
		}
		
		if($rightChildNode < $this->size && $this->queueItems[$rightChildNode]['Priority'] < $this->queueItems[$node]['Priority']) {
			$largestNode = $rightChildNode;
		}
		
		if($largestNode != $node) {
			$this->swapArrayElements($node, $largestNode);
			$this->minHeapify($largestNode);
		}
	}
	
	public function __toString() {
		$stringRepresentation = '';
		for($counter = 0; $counter < $this->size; $counter++) {
			$stringRepresentation .= (string)$this->queueItems[$counter]['Priority'] . ' ' .
			                         (string)$this->queueItems[$counter]['Data'] . '<br />';
		}
		
		return $stringRepresentation;
	}
	
	private function swapArrayElements($elementOne, $elementTwo) {		
		$swap = $this->queueItems[$elementOne];
		$this->queueItems[$elementOne] = $this->queueItems[$elementTwo];
		$this->queueItems[$elementTwo] = $swap;
	}
}

?>