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
