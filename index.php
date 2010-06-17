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

require_once('range_encoder_2.php');
require_once('src/WriteStream.php');
require_once('src/RangeEncoder.php');

/*$unc = file_get_contents('moby_un.txt');
$comp = new CompressionStream(new ReadStream($unc));
$compString = (string) $comp;
file_put_contents('moby_c.txt', $compString);

$com = file_get_contents('moby_c.txt');
$dec = new DecompressionStream(new ReadStream($com));
$decString = (string) $dec;
file_put_contents('moby_un2.txt', $decString);*/

/*$cs = new CompressionStream2();
$cs->write(file_get_contents('moby_un.txt'));
$cs->flush();
echo $cs;
file_put_contents('moby-cs.txt', $cs);*/

/*$stream = new ReadStream('Hello World');
$arr = array();
echo $stream->readCharsToArray($arr, 2, 5);
print_r($arr);*/

$streamOne = new WriteStream();
$streamTwo = new WriteStream();

$encOne = new RangeEncoder2($streamOne);
$encTwo = new RangeEncoder($streamTwo, true);
/*
$encOne->encode(5, 7, 16);
$encOne->encode(2, 19, 32);
$encOne->encode(3, 4, 8);
$encOne->encode(0, 5, 64);
$encOne->flush();
*/
echo $encTwo->encodeSymbol(5, 7, 16) . '<br />';
echo $encTwo->encodeSymbol(2, 19, 32) . '<br />';
echo $encTwo->encodeSymbol(3, 4, 8) . '<br />';
echo $encTwo->encodeSymbol(0, 5, 64) . '<br />';
echo $encTwo->close() . '<br />';

echo (string)$encOne->getStream() . "<br />" . (string)$encTwo->getStream();
/*
for($xor = 0x80 ^ 0x90, $n = 8; $xor >= pow(2, 8 - $n); $n--);

for($i = 1; $i < 256; $i = $i + 4) {
	for($j = 3; $j < 256; $j = $j + 5) {
		for($xor = $i ^ $j, $n = 8; $xor >= pow(2, 8 - $n); $n--);
		$nn = countStableBits($i, $j);
		echo dechex($i) . ' ' . dechex($j) . ": $n, $nn<br />";
		if($n != $nn) {
			echo 'PROBLEM HERE!!!<br />PROBLEM HERE!!!<br />PROBLEM HERE!!!<br />';
		}
	}
}

function countStableBits($byteOne, $byteTwo) {
		$xorOfBytes = $byteOne ^ $byteTwo;
		
		if($xorOfBytes < 1) {
			return 8;
		} elseif($xorOfBytes < 2) {
			return 7;
		} elseif($xorOfBytes < 4) {
			return 6;
		} elseif($xorOfBytes < 8) {
			return 5;
		} elseif($xorOfBytes < 16) {
			return 4;
		} elseif($xorOfBytes < 32) {
			return 3;
		} elseif($xorOfBytes < 64) {
			return 2;
		} elseif($xorOfBytes < 128) {
			return 1;
		} else {
			return 0;
		}
	}*/

?>