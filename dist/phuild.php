#!/usr/bin/php
<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */
/*
 * We need to prevent that ComponentsNeeded tries to load classes which were
 * already declared by PHP. However, we also need to prevent that
 * ComponentsNeeded doesn't load classes which were declared within the script
 * itself! Therefore, we get all of PHP's classes & interfaces at the very
 * beginning of the script and use it for further reference on which classes
 * should be ignored or not.
 */
$ignore = array_merge(get_declared_classes(), get_declared_interfaces());
/**
 * We of course add self and parent, 
 */
$ignore[] = "self";
$ignore[] = "parent";
#Include
#Imported from ./include/local/ComponentsAvailable.php

/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
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
#Imported from ./include/local/ComponentsNeeded.php

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
#Imported from ./include/local/Main.php

/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */
class Main {
	private $file;
	private $target;
	private $sourcePath;
	private $needed;
	private $available;
	function __construct(array $argv, array $ignore) {
		if(!file_exists($argv[1])) {
			throw new Exception("file does not exist.");
		}
		$this->file = $argv[1];
		$this->sourcePath = dirname($this->file);
		$this->available = new ComponentsAvailable($this->sourcePath);
		$this->needed = new ComponentsNeeded($this->file, $this->available, $ignore);
	}
	
	function run() {
		echo $this->needed->replace(ComponentsNeeded::SOURCE);
	}
}
#/Include

try {
	$main = new Main($argv, $ignore);
	$main->run();
} catch (Exception $ex) {
	echo $ex->getMessage().PHP_EOL;
}