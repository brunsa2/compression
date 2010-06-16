<?php

require_once('PHPUnit/Framework.php');
require_once('../write_stream.php');

class WriteStreamTest extends PHPUnit_Framework_TestCase {
	public function testWriteInt() {
		$stream = new WriteStream();
		$stream->writeInt(65);
		$this->assertEquals((string) $stream, 'A');
	}
	
	public function testWrite() {
		$stream = new WriteStream();
		
	}
}

?>