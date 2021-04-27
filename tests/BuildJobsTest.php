<?php
/**
 * @copyright (c) 2021, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */
declare(strict_types=1);
use PHPUnit\Framework\TestCase;

/**
 * Build Jobs Test
 * 
 * Test is quite shallow because BuildJobs is little more than a wrapper around
 * BuildJob/ImportJob.
 */
class BuildJobsTest extends TestCase {
	function testFromDirectory() {
		$jobs = BuildJobs::fromDirectory(__DIR__);
		$this->assertInstanceOf(BuildJobs::class, $jobs);
	}
	
	function testFromDirectoryNoFile() {
		$this->expectException(InvalidArgumentException::class);
		$jobs = BuildJobs::fromDirectory(__DIR__."/example/");
	}

	function testFromDirectoryIsFile() {
		$this->expectException(InvalidArgumentException::class);
		$jobs = BuildJobs::fromDirectory(__DIR__."/phuild.yml");
	}

	
	function testFromFile() {
		$jobs = BuildJobs::fromFile(__DIR__."/phuild.yml");
		$this->assertInstanceOf(BuildJobs::class, $jobs);
	}
	
	function testGetCount() {
		$jobs = BuildJobs::fromFile(__DIR__."/phuild.yml");
		$this->assertEquals(2, $jobs->getCount());
	}
	
	function testGetBuildJob() {
		$jobs = BuildJobs::fromFile(__DIR__."/phuild.yml");
		$this->assertInstanceOf(BuildJob::class, $jobs->getBuildJob(0));
		$this->assertInstanceOf(BuildJob::class, $jobs->getBuildJob(1));
	}
	
	function testGetNonExistentBuildJob() {
		$jobs = BuildJobs::fromFile(__DIR__."/phuild.yml");
		$this->expectException(OutOfBoundsException::class);
		$jobs->getBuildJob(3);
	}
}
