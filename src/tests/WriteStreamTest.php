<?php

require_once('PHPUnit/Framework.php');
require_once('../WriteStream.php');

class WriteStreamTest extends PHPUnit_Framework_TestCase {
	public function testWriteInt() {
		$stream = new WriteStream();
		
		$stream->writeInt(65);
		$this->assertEquals((string) $stream, 'A');
	}
	
	public function testWrite() {
		$stream = new WriteStream();
		
		$stream->write(array('A', 'B', 'C'));
		$this->assertEquals((string) $stream, 'ABC');
		
		$stream->write(array('A', 'B', 'C'), 1);
		$this->assertEquals((string) $stream, 'ABCBC');
		
		$stream->write(array('A', 'B', 'C'), 1, 1);
		$this->assertEquals((string) $stream, 'ABCBCB');
		
		$stream->write('ABC');
		$this->assertEquals((string) $stream, 'ABCBCBABC');
		
		$stream->write('ABC', 1);
		$this->assertEquals((string) $stream, 'ABCBCBABCBC');
		
		$stream->write('ABC', 1, 1);
		$this->assertEquals((string) $stream, 'ABCBCBABCBCB');
		
		$stream->write(123);
		$this->assertEquals((string) $stream, 'ABCBCBABCBCB123');
	}
}

?>