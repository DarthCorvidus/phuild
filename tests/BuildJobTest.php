<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

/**
 * Test for BuildJob is quite shallow, as most of the validation is done
 * within ImportJob and therefore tested by ImportJob.
 */
class BuildJobTest extends TestCase {
	/**
	 * Get default array
	 * 
	 * Get a default test array (valid).
	 * @return array
	 */
	function getDefaultArray(): array {
		$array["name"] = "Some name";
		$array["source"] = "example/characters.php";
		$array["target"] = "dist/characters.php";
		$array["includes"] = array("lib01", "lib02");
	return $array;
	}
	
	/**
	 * Test from array
	 * 
	 * Test to construct from array.
	 */
	function testFromArray() {
		$job = BuildJob::fromArray($this->getDefaultArray());
		$this->assertInstanceOf(BuildJob::class, $job);
	}
	
	/**
	 * Test get name
	 * 
	 * Test to get name.
	 */
	function testGetName() {
		$job = BuildJob::fromArray($this->getDefaultArray());
		$this->assertEquals("Some name", $job->getName());
	}

	/**
	 * Test get source
	 * 
	 * Test to get source.
	 */
	function testGetSource() {
		$job = BuildJob::fromArray($this->getDefaultArray());
		$this->assertEquals("example/characters.php", $job->getSource());
	}

	/**
	 * Test get target
	 * 
	 * Test to get target.
	 */
	function testGetTarget() {
		$job = BuildJob::fromArray($this->getDefaultArray());
		$this->assertEquals("dist/characters.php", $job->getTarget());
	}
	
	/**
	 * Test get includes
	 * 
	 * Test to get includes.
	 */
	function testGetIncludes() {
		$job = BuildJob::fromArray($this->getDefaultArray());
		$this->assertEquals(array("lib01", "lib02"), $job->getIncludes());
	}
}
