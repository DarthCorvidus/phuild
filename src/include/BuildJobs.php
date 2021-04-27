<?php
/**
 * @copyright (c) 2021, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */

/**
 * BuildJobs
 * 
 * Class to represent a collection of build jobs.
 */

class BuildJobs {
	private $directory;
	private $buildJobs = array();
	/**
	 * Constructor
	 * 
	 * Constructor is private because factory methods have to be used.
	 */
	private function __construct() {
		;
	}
	
	/**
	 * fromDirectory
	 * 
	 * Create instance of BuildJobs from directory (assumes that phuild.yml
	 * exist in directory).
	 * @param type $directory
	 * @return \BuildJobs
	 */
	static function fromDirectory($directory): BuildJobs {
		Assert::fileExists($directory);
		Assert::isDir($directory);
		return BuildJobs::fromFile($directory."/phuild.yml");
	}
	
	/**
	 * fromFile
	 * 
	 * Create instance of BuildJobs from file.
	 * @param string $file
	 * @return \BuildJobs
	 */
	static function fromFile(string $file): BuildJobs {
		Assert::fileExists($file);
		Assert::isFile($file);

		$buildjobs = new BuildJobs();
		$buildjobs->directory = dirname($file);
		
		$parsed = yaml_parse_file($file);
		foreach($parsed as $key => $value) {
			$buildjobs->buildJobs[] = BuildJob::fromArray($value);
		}
		
	return $buildjobs;
	}
	
	/**
	 * getCount
	 * 
	 * Returns the number of build jobs contained within.
	 * @return int
	 */
	function getCount(): int {
		return count($this->buildJobs);
	}
	
	/**
	 * getBuildJob
	 * 
	 * Retrieves an instance of BuildJob and throws OutOfBoundsException if
	 * illegal item is requested.
	 * @param type $item
	 * @return \BuildJob
	 * @throws OutOfBoundsException
	 */
	function getBuildJob($item): BuildJob {
		if(!isset($this->buildJobs[$item])) {
			throw new OutOfBoundsException("Build job ".$item." does not exist.");
		}
		return $this->buildJobs[$item];
	}
	
}