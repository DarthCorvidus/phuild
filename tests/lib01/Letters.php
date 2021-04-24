<?php
class Letters implements Character {
	private $letters = array();
	function __construct() {
		for($i= ord("A"); $i<= ord("X");$i++) {
			$this->letters[] = chr($i);
		}
		for($i= ord("a"); $i<= ord("b");$i++) {
			$this->letters[] = chr($i);
		}
	}

	public function getCharacter(int $i): string {
		return $this->letters[$i];
	}

	public function getCount(): int {
		return count($this->letters);
	}
}
