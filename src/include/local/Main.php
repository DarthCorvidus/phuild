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
	private $jobs;
	private $root;
	private $ignore = array();
	function __construct(array $argv, array $ignore) {
		$this->ignore = $ignore;
		$model = new ArgvMain();
		if(!isset($argv[1])) {
			$reference = new ArgvReference($model);
			$reference->getReference();
			die();
		}
		$this->argv = new Argv($argv, $model);
	
		if(is_dir($this->argv->getPositional(0))) {
			$this->root = $this->argv->getPositional(0);
			$this->jobs = BuildJobs::fromDirectory($this->root);
		}

		if(is_file($this->argv->getPositional(0))) {
			$this->root = dirname($this->argv->getPositional(0));
			$this->jobs = BuildJobs::fromFile($this->argv->getPositional(0));
		}
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
	
	private function runJob(BuildJob $job) {
		foreach($job->getIncludes() as $key => $value) {
			if($key == 0) {
				$available = new ComponentsAvailable($this->root."/".$value);
				continue;
			}
			$available->addDirectory($this->root."/".$value);
		}
		
		$needed = new ComponentsNeeded($this->root."/".$job->getSource(), $available, $this->ignore);
		if($this->argv->getBoolean("check")) {
			$needed->check();
			return;
		}

		$replaced = $needed->replace(ComponentsNeeded::SOURCE);
		echo "Saving ".$this->root."/".$job->getTarget()."...";
		file_put_contents($this->root."/".$job->getTarget(), $replaced);
		echo "Done!".PHP_EOL;
	}
	
	function run() {
		for($i=0;$i<$this->jobs->getCount();$i++) {
			$this->runJob($this->jobs->getBuildJob($i));
		}
	}
}
