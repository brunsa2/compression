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

$encOne->encode(5, 7, 16);
$encOne->encode(2, 19, 32);
$encOne->encode(3, 4, 8);
$encOne->flush();

$encTwo->encodeSymbol(5, 7, 16);
$encTwo->encodeSymbol(2, 19, 32);
$encTwo->encodeSymbol(3, 4, 8);
$encTwo->close();

echo (string)$encOne->getStream() . "<br />" . (string)$encTwo->getStream();

?>