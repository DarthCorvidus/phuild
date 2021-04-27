<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */

/**
 * BuildJob
 * 
 * Class representing a build job.
 */
class BuildJob {
	private $name;
	private $source;
	private $target;
	private $includes = array();
	static function fromArray(array $array): BuildJob {
		$import = new Import($array, new ImportJob());
		$array = $import->getArray();
		$buildjob = new BuildJob();
		$buildjob->name = $array["name"];
		$buildjob->source = $array["source"];
		$buildjob->target = $array["target"];
		$buildjob->includes = $array["includes"];
	return $buildjob;
	}
	
	/**
	 * getName
	 * 
	 * Gets name of build job.
	 * @return string
	 */
	function getName(): string {
		return $this->name;
	}
	
	/**
	 * getSource
	 * 
	 * Get source file to work upon.
	 * @return string
	 */
	function getSource(): string {
		return $this->source;
	}

	/**
	 * getTarget
	 * 
	 * Get target file for consolidated file.
	 * @return string
	 */
	function getTarget(): string {
		return $this->target;
	}

	/**
	 * getIncludes
	 * 
	 * Get include directories to look in for dependencies.
	 * @return array
	 */
	function getIncludes(): array {
		return $this->includes;
	}
}