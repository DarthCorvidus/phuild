<?php
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
	private $argv;
	function __construct(array $argv, array $ignore) {
		if(!isset($argv[1])) {
			throw new Exception("Usage: phuild.php <filename> [parameters]");
			die();
		}
		if(!file_exists($argv[1])) {
			throw new Exception("file does not exist.");
		}
		$model = new ArgvMain();
		$this->argv = new Argv($argv, $model);
		$boolean = array("source", "require", "check");
		$boolCount = 0;
		foreach($boolean as $value) {
			if($this->argv->getBoolean($value)) {
				$boolCount++;
			}
			if($boolCount>1) {
				throw new Exception("--source, --check and --require are mutually exclusive.");
			}
		}
		if($boolCount==0) {
			throw new Exception("Needs --source, --check or --require");
		}
		$this->file = $argv[1];
		$this->sourcePath = dirname($this->file);
		$this->available = new ComponentsAvailable($this->sourcePath);
		$this->needed = new ComponentsNeeded($this->file, $this->available, $ignore);
	}
	
	private function saveFile() {
		if($this->argv->getBoolean("check")) {
			return;
		}
		if($this->argv->getBoolean("source")) {
			$replaced = $this->needed->replace(ComponentsNeeded::SOURCE);
		}
		if($this->argv->getBoolean("require")) {
			$replaced = $this->needed->replace(ComponentsNeeded::REQONCE);
		}
		if(!$this->argv->hasValue("output")) {
			echo $replaced.PHP_EOL;
			return;
		}
		if(file_exists($this->argv->getValue("output")) && !$this->argv->getBoolean("force")) {
			throw new Exception("file ".$this->argv->getValue("output")." already exists, use --force to replace.");
		}
		file_put_contents($this->argv->getValue("output"), $replaced);
	}
	
	function run() {
		$this->saveFile();
		if($this->argv->getBoolean("check")) {
			$this->needed->check();
		}
	}
}
