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
	private $arg = array();
	private $positional = array();
	private $positionalNames = array();
	public function __construct() {
		$this->arg["output"] = new ArgString();
		$this->positional[] = new ArgString();
		$this->positionalNames[] = "source";
	}
	public function getBoolean(): array {
		return array("source", "require", "check", "force");
	}

	public function getArgNames(): array {
		return array_keys($this->arg);
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
