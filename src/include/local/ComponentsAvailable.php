<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Components
 *
 * @author hm
 */
class ComponentsAvailable {
	private $folder;
	private $files;
	private $classes;
	private $sort;
	function __construct(string $folder) {
		$this->folder = realpath($folder);
		$this->recurse($this->folder);
	}
	
	private function recurse($folder) {
		foreach(glob($folder."/*") as $value) {
			$info = pathinfo($value);
			if(is_dir($value)) {
				$this->recurse($value);
				continue;
			}
			if(!isset($info["extension"])) {
				continue;
			}
			if($info["extension"]!="php") {
				continue;
			}
			$this->parse($value);
		}
	}
	
	private function parse($file) {
		$string = file_get_contents($file);
		$tokens = token_get_all($string);

		$interesting = array(T_CLASS, T_INTERFACE);
		foreach($tokens as $key => $value) {
			if(!is_array($value)) {
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
			$this->classes[$tokens[$key+2][1]] = $file;
		}
	}
	
	public function getComponent(string $component) {
		if(!isset($this->classes[$component])) {
			throw new Exception("component ".$component." not known.");
		}
		return $this->classes[$component];
	}
	
	public function getRequireOnce(string $component) {
		if(!isset($this->classes[$component])) {
			throw new Exception("component ".$component." not known.");
		}
		$path = $this->getComponent($component);
		return "require_once __DIR__.\"".str_replace($this->folder, "", $path)."\";";
	}
}
