<?php

require_once('PHPUnit/Framework.php');
require_once('../ReadStream.php');

class ReadStreamTest extends PHPUnit_Framework_TestCase {
	public function testReadReadCharPeekAndPeekChar() {
		$stream = new ReadStream('ABC');
		
		$this->assertEquals($stream->read(), 65);
		$this->assertEquals($stream->readChar(), 'B');
		$this->assertEquals($stream->peek(), 67);
		$this->assertEquals($stream->peekChar(), 'C');
		$this->assertEquals($stream->readChar(), 'C');
		$this->assertEquals($stream->read(), -1);
	}
	
	public function testReadToArray() {
		$stream = new ReadStream('ABC');
		$arrayFromStream = array();
		
		$count = $stream->readToArray($arrayFromStream);
		$this->assertEquals(3, $count);
		$this->assertEquals($arrayFromStream[0], 65);
		$this->assertEquals($arrayFromStream[1], 66);
		$this->assertEquals($arrayFromStream[2], 67);
		
		$stream = new ReadStream('ABC');
		$arrayFromStream = array();
		
		$count = $stream->readToArray($arrayFromStream, 1);
		$this->assertEquals(3, $count);
		$this->assertEquals($arrayFromStream[1], 65);
		$this->assertEquals($arrayFromStream[2], 66);
		
		$stream = new ReadStream('ABC');
		$arrayFromStream = array();
		
		$count = $stream->readToArray($arrayFromStream, 1, 1);
		$this->assertEquals($count, 1);
		$this->assertEquals($arrayFromStream[1], 65);
	}
	
	public function testReadCharsToArray() {
		$stream = new ReadStream('ABC');
		$arrayFromStream = array();
		
		$count = $stream->readCharsToArray($arrayFromStream);
		$this->assertEquals(3, $count);
		$this->assertEquals($arrayFromStream[0], 'A');
		$this->assertEquals($arrayFromStream[1], 'B');
		$this->assertEquals($arrayFromStream[2], 'C');
		
		$stream = new ReadStream('ABC');
		$arrayFromStream = array();
		
		$count = $stream->readCharsToArray($arrayFromStream, 1);
		$this->assertEquals(3, $count);
		$this->assertEquals($arrayFromStream[1], 'A');
		$this->assertEquals($arrayFromStream[2], 'B');
		
		$stream = new ReadStream('ABC');
		$arrayFromStream = array();
		
		$count = $stream->readCharsToArray($arrayFromStream, 1, 1);
		$this->assertEquals($count, 1);
		$this->assertEquals($arrayFromStream[1], 'A');
	}
	
	/**
	 * @depends testReadReadCharPeekAndPeekChar
	 */
	public function testResetAndMark() {
		$stream = new ReadStream('AB');
		
		$this->assertEquals($stream->readChar(), 'A');
		$stream->reset();
		$this->assertEquals($stream->readChar(), 'A');
		$stream->mark(1);
		$stream->reset();
		$this->assertEquals($stream->readChar(), 'B');
		$stream->reset();
		$this->assertEquals($stream->readChar(), 'B');
	}
	
	/**
	 * @depends testReadReadCharPeekAndPeekChar
	 * @depends testResetAndMark
	 */
	public function testAtEndCloseAndIsClosed() {
		$stream = new ReadStream('A');
		
		$this->assertEquals($stream->atEnd(), false);
		$stream->read();
		$this->assertEquals($stream->atEnd(), true);
		
		$stream->reset();
		
		$this->assertEquals($stream->isClosed(), false);
		$stream->close();
		$this->assertEquals($stream->isClosed(), true);
	}
	
	/**
	 * @depends testReadReadCharPeekAndPeekChar
	 */
	public function testSkip() {
		$stream = new ReadStream('ABC');
		
		$this->assertEquals($stream->skip(1), 1);
		$this->assertEquals($stream->readChar(), 'B');
		$this->assertEquals($stream->skip(-2), -2);
		$this->assertEquals($stream->readChar(), 'A');
		$this->assertEquals($stream->skip(10), 2);
		$this->assertEquals($stream->readChar(), '');
	}
	
	/**
	 * depends testReadReadCharPeekAndPeekChar
	 */
	public function testToString() {
		$stream = new ReadStream('ABC');
		$stream->read();
		
		$this->assertEquals((string) $stream, 'ABC');
	}
}

?>