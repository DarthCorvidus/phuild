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
	/**
	 * Constructor
	 * 
	 * Needs a file to start upon and ComponentsAvailable to look up further
	 * files containing dependencies.
	 * $ignore contains component names which should be ignored altogether - for
	 * instance, PHP's builtin classes, which are determined by phuild.php right
	 * at the start of the script before anything else is done.
	 * 
	 * @param type $file
	 * @param ComponentsAvailable $ca
	 * @param array $ignore
	 */
	function __construct($file, ComponentsAvailable $ca, array $ignore) {
		#Assert::fileExists($file);
		if(!file_exists($file)) {
			throw new InvalidArgumentException("directory ".$file." does not exist.");
		}
		$this->main = realpath($file);
		$this->components = $ca;
		$this->ignore = $ignore;
		$this->parse($file);
	}

	/**
	 * AddClass
	 * 
	 * AddClass() adds a component to the stack of necessary components and
	 * parses deeper into the class hierarchy, IE if the first file showed that
	 * class Dog is needed, the file Dog will be parsed to determine further
	 * dependencies.
	 * 
	 * @param type $className
	 * @return type
	 */
	private function addClass($className) {
		if(in_array($className, $this->ignore)) {
			return;
		}
		if(!$this->components->hasComponent($className)) {
			$this->classes[] = $className;
			return;
		}

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
	
	/**
	 * checkClassname
	 * 
	 * Checks if a classname is valid. By now, only variables instead of class
	 * names will result in a RuntimeException.
	 * @param type $classname
	 * @param type $filename
	 * @param int $line
	 * @throws RuntimeException
	 */
	private static function checkClassname($classname, $filename, int $line) {
		if($classname[0]=="\$") {
			throw new RuntimeException("class name ".$classname." in ".$filename." line ".$line." contains a variable.");
		}
	}
	
	/**
	 * extract needed
	 * 
	 * extractNeeded parses a file using PHP's own tokenizer. It then
	 * specifically looks for T_IMPLEMENTS, T_NEW, T_EXTENDS and T_DOUBLE_COLON,
	 * aka T_PAAMAYIM_NEKUDOTAYIM to determine which classes/interfaces are used
	 * within a file.
	 * Tokens between #Include and #/Include are skipped.
	 * 
	 * A runtime exception is thrown if a variable is used for a classname,
	 * ie new $name();. There is no way for phuild to determine the content.
	 * 
	 * @param string $file
	 * @return array
	 * @throws RuntimeException
	 */
	static function extractNeeded(string $file): array {
		$needed = array();
		$string = file_get_contents($file);
		$tokens = token_get_all($string);
		$interesting = array(T_IMPLEMENTS, T_EXTENDS, T_NEW);
		$ignore = FALSE;
		foreach($tokens as $key => $value) {
			if(!is_array($value)) {
				continue;
			}
			/*
			 * We have to ignore any #Include-Block, allowing the developer
			 * to for instance use a custom autoloader without it counting as
			 * a required component. 
			 */
			if($value[0] == T_COMMENT && trim($value[1]) == "#Include") {
				$ignore = true;
				continue;
			}
			if($value[0] == T_COMMENT && trim($value[1]) == "#/Include") {
				$ignore = FALSE;
				continue;
			}
			if($ignore==TRUE) {
				continue;
			}
			
			if($value[0]==T_DOUBLE_COLON) {
				$className = $tokens[$key-1][1];
				self::checkClassname($className, $file, $tokens[$key-1][2]);
				$needed[] = $className;
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
			self::checkClassname($className, $file, $tokens[$key+2][2]);
			$needed[] = $className;
		}
	return $needed;
	}
	
	/**
	 * parse
	 * 
	 * parse() does little more than to call extractNeeded and then addClass on
	 * every result.
	 * @param type $file
	 */
	private function parse($file) {
		$components = self::extractNeeded($file);
		foreach($components as $className) {
			$this->addClass($className);
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
	
	private function printCheckResult(array $array, string $heading) {
		foreach($array as $key => $value) {
			if($key==0) {
				echo $heading.PHP_EOL;
			}
			echo "\t".$value.PHP_EOL;
		}
	}
	
	function check() {
		$available = array();
		$missing = array();
		foreach($this->classes as $value) {
			if($this->components->hasComponent($value)) {
				$available[] = $value;
			} else {
				$missing[] = $value;
			}
		}
		$this->printCheckResult($available, "Available Components:");
		$this->printCheckResult($missing, "Missing Components:");
	}
}
