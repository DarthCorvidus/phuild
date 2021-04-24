<?php
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
	/**
	 * 
	 * @param string $directory
	 */
	function __construct(string $directory) {
		#$this->folder = realpath($folder);
		$this->addDirectory($directory);
		#$this->recurse($this->folder);
	}
	
	/**
	 * Add Directory
	 * 
	 * Add another directory to an instance of ComponentsAvailable
	 * @param string $directory Directory containing PHP files
	 */
	function addDirectory(string $directory) {
		Assert::fileExists($directory);
		Assert::isDir($directory);
		$this->recurse(realpath($directory));
	}

	/**
	 * Add File
	 * 
	 * Add a single file containing PHP classes.
	 * @param type $file
	 */
	function addFile($file) {
		Assert::fileExists($file);
		Assert::isFile($file);
		$this->parse($file);
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
	
	/**
	 * Has Component
	 * 
	 * Checks if an instance of ComponentsAvailable knows the whereabouts of a
	 * specific component.
	 * @param string $component
	 * @return bool
	 */
	public function hasComponent(string $component):bool {
		return isset($this->classes[$component]);
	}
	
	/**
	 * Get Component
	 * 
	 * Returns the path of the file that contains a certain Component. Throws an
	 * Exception if a Component cannot be resolved to a file.
	 * @param string $component
	 * @return type
	 * @throws Exception
	 */
	public function getComponent(string $component) {
		if(!$this->hasComponent($component)) {
			throw new Exception("component ".$component." not known.");
		}
		return $this->classes[$component];
	}
	
	/**
	 * Get Components
	 * 
	 * Get the components gathered by ComponentsAvailable.
	 * @return array
	 */
	function getComponents(): array {
		sort($this->classes);
	return $this->classes;
	}
}