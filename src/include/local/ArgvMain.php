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
	public function getArgModel(int $arg): \ArgModel {
		
	}

	public function getBoolean(): array {
		return array("source", "require", "check");
	}

	public function getParamCount(): int {
		return 0;
	}

}
