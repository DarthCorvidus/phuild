<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ArgvMain
 *
 * @author hm
 */
class ArgvMain implements ArgvModel{
	private $positional = array();
	private $positionalNames = array();
	public function __construct() {
		$this->positional[0] = new ArgGeneric();
		$this->positional[0]->setMandatory();
		$this->positional[0]->setConvert(new ConvertTrailingSlash());
		$this->positionalNames[] = "build configuration";
	}
	public function getBoolean(): array {
		return array("check");
	}

	public function getArgNames(): array {
		return array();
	}

	public function getNamedArg(string $name): \ArgModel {
		return $this->arg[$name];
	}

	public function getPositionalArg(int $i): \ArgModel {
		return $this->positional[$i];
	}

	public function getPositionalCount(): int {
		return count($this->positional);
	}

	public function getPositionalName(int $i): string {
		return $this->positionalNames[$i];
	}

}
