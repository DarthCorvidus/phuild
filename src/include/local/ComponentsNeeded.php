<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ComponentsNeeded
 *
 * @author hm
 */
class ComponentsNeeded {
	private $main;
	private $classes = array();
	private $components;
	private $ignore = array();
	function __construct($file, ComponentsAvailable $ca, array $ignore) {
		$this->main = realpath($file);
		$this->components = $ca;
		$this->ignore = $ignore;
		$this->parse($file);
	}

	private function addClass(string $file, string $className) {
		if(!in_array($className, $this->ignore)) {
			/**
			 * Fill ignore first, to prevent that classes get added for several
			 * times.
			 */
			$this->ignore[] = $className;
			$this->parse($this->components->getComponent($className));
			/**
			 * Fill classes after parse, to ensure proper dependency, ie what's
			 * needed is added before what needs it.
			 */
			$this->classes[] = $className;
		}
	}
	
	private function parse($file) {
		$string = file_get_contents($file);
		$tokens = token_get_all($string);

		$interesting = array(T_IMPLEMENTS, T_EXTENDS, T_NEW);
		foreach($tokens as $key => $value) {
			if(!is_array($value)) {
				continue;
			}
			if($value[0]==T_DOUBLE_COLON) {
				$className = $tokens[$key-1][1];
				$this->addClass($file, $className);
				continue;
			}
			if(!in_array($value[0], $interesting)) {
				continue;
			}
			if(!isset($tokens[$key+1]) || !isset($tokens[$key+2])) {
				continue;
			}
			if($tokens[$key+1][0]!=T_WHITESPACE) {
				continue;
			}
			$className = $tokens[$key+2][1];
			$this->addClass($file, $className);
		}
	}
	
	function getClasses(): array {
		return $this->classes;
	}
	
	function getRequireOnce(): string {
		$require = "";
		foreach($this->classes as $key => $value) {
			$require .= "require_once ".str_replace(dirname($this->main), "__DIR__.'", $this->components->getComponent($value))."';".PHP_EOL;
		}
	return $require;
	}
}
