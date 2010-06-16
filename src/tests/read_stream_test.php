<?php

require_once('PHPUnit/Framework.php');
require_once('../read_Stream.php');

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
		$stream = new ReadStream('ABCBCB');
		
		$arrayFromStream = array();
		
		$count = $stream->readToArray($arrayFromStream);
		$this->assertEquals($count, 3);
		$this->assertEquals($arrayFromStream[0], 65);
		$this->assertEquals($arrayFromStream[0], 66);
		$this->assertEquals($arrayFromStream[0], 67);
		
		$arrayFromStream = array();
		
		$count = $stream->readToArray($arrayFromStream, 1);
		$this->assertEquals($count, 3);
		$this->assertEquals($arrayFromStream[1], 66);
		$this->assertEquals($arrayFromStream[2], 67);
		
		$arrayFromStream = array();
		
		$count = $stream->readToArray($arrayFromStream, 1, 1);
		$this->assertEquals($count, 3);
		$this->assertEquals($arrayFromStream[1], 66);
	}
	
	public function testReadCharsToArray() {
		$stream = new ReadStream('ABCBCB');
		
		$arrayFromStream = array();
		
		$count = $stream->readCharsToArray($arrayFromStream);
		$this->assertEquals($count, 3);
		$this->assertEquals($arrayFromStream[0], 65);
		$this->assertEquals($arrayFromStream[0], 66);
		$this->assertEquals($arrayFromStream[0], 67);
		
		$arrayFromStream = array();
		
		$count = $stream->readCharsToArray($arrayFromStream, 1);
		$this->assertEquals($count, 3);
		$this->assertEquals($arrayFromStream[1], 66);
		$this->assertEquals($arrayFromStream[2], 67);
		
		$arrayFromStream = array();
		
		$count = $stream->readCharsToArray($arrayFromStream, 1, 1);
		$this->assertEquals($count, 3);
		$this->assertEquals($arrayFromStream[1], 66);
	}
	
	/**
	 * @depends testReadReadCharPeekAndPeekChar
	 */
	public function testResetAndMark() {
		$stream = new ReadString('AB');
		
		$this->assertEquals($stream->readChar(), 'A');
		$this->reset();
		$this->assertEquals($stream->readChar(), 'A');
		$this->mark(1);
		$this->reset();
		$this->assertEquals($stream->readChar(), 'B');
		$this->reset();
		$this->assertEquals($stream->readChar(), 'B');
	}
	
	/**
	 * @depends testReadReadCharPeekAndPeekChar
	 * @depends testResetAndMark
	 */
	public function testAtEndCloseAndIsClosed() {
		$stream = new ReadStream('A');
		
		$this->assertEquals($stream->atEnd(), false);
		$this->read();
		$this->assertEquals($stream->atEnd(), true);
		
		$this->reset();
		
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