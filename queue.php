<?php

class Queue {
	private $head;
	private $tail;
	private $size;
	
	public function __construct() {
		$this->head = null;
		$this->tail = null;
		$this->size = 0;
	}
	
	public function enqueue($data) {
		$newEntry = new QueueEntry();
		$newEntry->data = $data;
		$newEntry->next = null;
		
		if($this->head == null) {
			$this->head = $newEntry;
			$this->tail = $newEntry;
		} else {
			$this->tail->next = $newEntry;
			$this->tail = $newEntry;
		}
		
		$this->size++;
	}
	
	public function dequeue() {
		if($this->head == null) {
			return null;
		} else {
			$queueData = $this->head->data;
			$this->head = $this->head->next;
			
			$this->size--;
			
			return $queueData;
		}
	}
	
	public function peek() {
		if($this->head == null) {
			return null;
		} else {
			return $this->head->data;
		}
	}
	
	public function isFull() {
		return ($this->head != null);
	}
	
	public function size() {
		return $this->size;
	}
}

class QueueEntry {
	public $data;
	public $next;
}

?>