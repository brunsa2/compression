<?php

function __autoload($class) {
	$fileName = '';
	for($character = 0; $character < strlen($class); $character++) {
		if((strtoupper(substr($class, $character, 1)) == substr($class, $character, 1)) && $character > 0) {
			$fileName .= '_';
		}
		
		$fileName .= strtolower(substr($class, $character, 1));
	}
	
	include_once($fileName . '.php');
}

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

$x = 10;
echo $x . '<br />';
$y = ($x/=5) * 2;
echo $x . '<br />';
echo $y . '<br />';


?>