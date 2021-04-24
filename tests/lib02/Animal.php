<?php
interface Animal {
	function getName(): string;
	function getSpecies(): string;
}

abstract class Mammal implements Animal {
	protected $name;
	function __construct($name) {
		$this->name = $name;
	}
	
	function getName(): string {
		return $this->name;
	}
}

class Dog extends Mammal {
	function getSpecies() {
		return "canis lupus familiaris";
	}
}
