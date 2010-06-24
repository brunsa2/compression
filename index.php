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

require_once('src/WriteStream.php');
require_once('src/ReadStream.php');
require_once('src/RangeEncoder.php');
require_once('src/RangeDecoder.php');
require_once('src/CompressionStream.php');
require_once('src/Order0Model.php');
require_once('src/DecompressionStream.php');
require_once('src/UInt128.php');

$i = new UInt128(12345);

echo $i->add(54321);
?>