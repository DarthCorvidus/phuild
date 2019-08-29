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
	public function __construct() {
		$file = new ArgString("output");
		$this->arg[] = $file;
	}
	public function getArgModel(int $arg): \ArgModel {
		return $this->arg[$arg];
	}

	public function getBoolean(): array {
		return array("source", "require", "check", "force");
	}

	public function getParamCount(): int {
		return count($this->arg);
	}

}
