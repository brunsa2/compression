<?php

/**
 * CompressionStream.php contains class {@link CompressionStream}.
 *
 * @author Jeff Stubler
 * @version 1.1
 * @package com.jeffstubler.compression
 */

/**
 * {@code CompressionStream} provides an order-0 model and a range encoder to compress data
 * written to it.
 *
 * @author Jeff Stubler
 * @version 1.1
 * @package com.jeffstubler.compression
 */

class CompressionStream {
	/**
	 * Range encoder used to write final data.
	 */
	protected $encoder;
	
	/**
	 * Order-0 model used to get cumulative frequencies.
	 */
	protected $model;
	
	/**
	 * Output stream data is written to.
	 */
	protected $outputStream;
	
	/**
	 * Initializes a new compression stream.
	 *
	 * @param boolean $forceThirtyTwoBitMath Forces the encoder to use 32-bit math.
	 */
	public function __construct($forceThirtyTwoBitMath = false) {
		$this->encoder = new RangeEncoder(new WriteStream(), $forceThirtyTwoBitMath);
		$this->model = new Order0Model();
		$this->outputStream = $this->encoder->getStream();
	}
	
	/**
	 * Write a string to the compression stream.
	 *
	 * @param mixed $data Data to compress.
	 */
	public function write($data = 0) {
		$data = (string) $data;
		
		for($currentCharacter = 0; $currentCharacter < strlen($data); $currentCharacter++) {
			$symbol = ord(substr($data, $currentCharacter, 1));
			
			$this->encoder->encodeSymbol($this->model->getLowFrequency($symbol), $this->model->getHighFrequency($symbol), $this->model->getFrequencyRange());
			$this->model->update($symbol, $this->encoder->getMaximumRange());
		}
	}
	
	/**
	 * Closes the stream.
	 */
	public function close() {
		$this->encoder->encodeSymbol($this->model->getLowFrequency(256), $this->model->getHighFrequency(256), $this->model->getFrequencyRange());
		$this->encoder->close();
	}
	
	/**
	 * Returns compressed data as a string.
	 *
	 * @return string Compressed data.
	 */
	public function __toString() {
		return (string) $this->outputStream;
	}
}

?>