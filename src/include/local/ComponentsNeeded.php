<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */
class ComponentsNeeded {
	private $main;
	private $classes = array();
	private $components;
	private $ignore = array();
	const REQONCE = 1;
	const SOURCE = 2;
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
	
	private function getRequireOnce(): string {
		$require = "";
		foreach($this->classes as $key => $value) {
			$require .= "require_once ".str_replace(dirname($this->main), "__DIR__.'", $this->components->getComponent($value))."';".PHP_EOL;
		}
	return $require;
	}
	
	private function getSource(): string {
		$replace = "";
		foreach($this->classes as $key => $value) {
			$file = trim(file_get_contents($this->components->getComponent($value)));
			$exp = explode(PHP_EOL, $file);
			$exp[0] = "#Imported from ".str_replace(dirname($this->main), ".", $this->components->getComponent($value)).PHP_EOL;
			if($exp[count($exp)-1]=="?>") {
				array_pop($exp);
			}
			$replace .= implode(PHP_EOL, $exp).PHP_EOL;
		}
	return $replace;
	}
	
	private function getReplace($type): string {
		$new = "";
		if($type==self::REQONCE) {
			$new .= $this->getRequireOnce();
		}
		if($type==self::SOURCE) {
			$new .= $this->getSource();
		}
	return $new;
	}
	
	function replace(int $type):string {
		$replace = false;
		$new = "";
		$handle = fopen($this->main, "r");
		while($line = fgets($handle)) {
			$trimmed = trim($line);
			if($trimmed=="#Include") {
				$new .= $line;
				$replace = true;
				continue;
			}
			if($trimmed=="#/Include") {
				$new .= $this->getReplace($type);
				$new .= $line;
				$replace = FALSE;
				continue;
			}
			if($replace==true) {
				continue;
			}
			$new .= $line;
		}
		fclose($handle);
	return $new;
	}
}
