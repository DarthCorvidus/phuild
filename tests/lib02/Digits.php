<?php
class Digits implements Character {
	private $digits = array();
	function __construct() {
		for($i= ord("0"); $i<= ord("9");$i++) {
			$this->digits[] = chr($i);
		}

	}

	public function getCharacter(int $i): string {
		return $this->digits[$i];
	}

	public function getCount(): int {
		return count($this->digits);
	}
}
