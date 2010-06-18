<?php
/*
function __autoload($class) {
	$fileName = '';
	for($character = 0; $character < strlen($class); $character++) {
		if((strtoupper(substr($class, $character, 1)) == substr($class, $character, 1)) && $character > 0) {
			$fileName .= '_';
		}
		
		$fileName .= strtolower(substr($class, $character, 1));
	}
	
	include_once($fileName . '.php');
}*/

require_once('deprecated/range_encoder_2.php');
require_once('deprecated/range_decoder_2.php');
require_once('deprecated/compression_stream.php');
require_once('deprecated/decompression_stream.php');
require_once('src/WriteStream.php');
require_once('src/ReadStream.php');
require_once('src/RangeEncoder.php');
require_once('src/RangeDecoder.php');
require_once('src/CompressionStream.php');
require_once('src/Order0Model.php');
require_once('src/DecompressionStream.php');
require_once('order_0_model.php');

$cstream = new CompressionStream(false);
$cstream->write('Data Data Data Data Data Data Data Data Data Data');
$cstream->close();
echo $cstream . '<br />Original length: ' . strlen('Data Data Data Data Data Data Data Data Data Data') . '<br />Compressed length: ' . strlen($cstream) . '<br />';

$dstream = new DecompressionStream(new ReadStream($cstream), false);
echo $dstream . '<br />';

?>