<?php

require_once('PHPUnit/Framework.php');
require_once('../CompressionStream.php');
require_once('../DecompressionStream.php');
require_once('../RangeEncoder.php');
require_once('../RangeDecoder.php');
require_once('../Order0Model.php');
require_once('../ReadStream.php');
require_once('../WriteStream.php');

class CompressionTest extends PHPUnit_Framework_TestCase {
	public function testCompression() {
		$compressionStream = new CompressionStream(false);
		$compressionStream->write('The quick brown fox jumps over a lazy dog. Data Data Data Data Data Data Data Data Data Data');
		$compressionStream->close();
		$decompressionStream = new DecompressionStream(new ReadStream($compressionStream), false);
		//echo $compressionStream . '<br />';
		//echo $decompressionStream->getStream() . '<br />';
		$this->assertEquals('The quick brown fox jumps over a lazy dog. Data Data Data Data Data Data Data Data Data Data', (string) $decompressionStream->getStream());
		
		if(strlen((string) $compressionStream) > strlen('The quick brown fox jumps over a lazy dog. Data Data Data Data Data Data Data Data Data Data')) {
			echo 'Potential problem: compressed size greater than original size.';
		}
	}
}

//$ct = new CompressionTest();
//$ct->testCompression();

?>