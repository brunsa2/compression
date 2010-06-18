<?php

/**
 * Order0Model.php contains class {@link Order0Model}.
 *
 * @author Jeff Stubler
 * @version 1.1
 * @package com.jeffstubler.compression
 */

/**
 * {@code Order0Model} provides a range decoder for decompression probabalistic symbols
 * from a {@link ReadStream}.
 *
 * @author Jeff Stubler
 * @version 1.1
 * @package com.jeffstubler.compression
 */

class Order0Model {
	/**
	 * Number of symbols this model represents, in this case 256 bytes and an EOF marker.
	 */
	const NUMBER_OF_SYMBOLS = 257;
	
	/**
	 * Cumulative frequency table.
	 */
	private $frequencies = array();
	
	/**
	 * Creates a new model object with default frequencies.
	 */
	public function __construct() {
		for($currentSymbol = 0; $currentSymbol < self::NUMBER_OF_SYMBOLS + 1; $currentSymbol++) {
			$this->frequencies[$currentSymbol] = $currentSymbol;
		}
	}
	
	/**
	 * Update the model's frequencies and rescale the frequencies if it would overflow the range encoder.
	 *
	 * @param integer $symbol Symbol number.
	 * @param integer $maximumRange Maximum frequency range to rescale to.
	 */
	public function update($symbol, $maximumRange) {
		for($currentSymbol = $symbol + 1; $currentSymbol < self::NUMBER_OF_SYMBOLS + 1; $currentSymbol++) {
			$this->frequencies[$currentSymbol]++;
		}
		
		if($this->frequencies[self::NUMBER_OF_SYMBOLS] > $maximumRange) {
			$total = 0;
			
			for($currentSymbol = 1; $currentSymbol < self::NUMBER_OF_SYMBOLS - 1; $currentSymbol++) {
				$total += (integer) ((($this->frequencies[$currentSymbol] - $this->frequencies[$currentSymbol - 1])/2) + 1);
				$this->frequencies[$currentSymbol] = $this->frequencies[$currentSymbol - 1] + ((($this->frequencies[$currentSymbol] - $this->frequencies[$currentSymbol - 1])/2) + 1);
			}
			
			$this->frequencies[self::NUMBER_OF_SYMBOLS - 1] = $total;
		}
	}
	
	/**
	 * Returns the low cumulative frequency of a symbol.
	 *
	 * @param integer @symbol Symbol to get frequency of.
	 * @return integer Low symbol cumulative frequency.
	 */
	public function getLowFrequency($symbol) {
		return $this->frequencies[$symbol];
	}
	
	/**
	 * Returns the low cumulative frequency of a symbol.
	 *
	 * @param integer @symbol Symbol to get frequency of.
	 * @return integer Low symbol cumulative frequency.
	 */
	public function getHighFrequency($symbol) {
		return $this->frequencies[$symbol + 1];
	}
	
	/**
	 * Returns the low cumulative frequency of a symbol.
	 *
	 * @return integer Total cumulative frequency range.
	 */
	public function getFrequencyRange() {
		return $this->frequencies[self::NUMBER_OF_SYMBOLS];
	}
}

?>