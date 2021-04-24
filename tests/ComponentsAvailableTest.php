<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
class ComponentsAvailableTest extends TestCase {
	/**
	 * Test Construct
	 * 
	 * Test simple construction.
	 */
	function testConstruct() {
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$this->assertInstanceOf("ComponentsAvailable", $ca);
	}
	
	/**
	 * Test Construct Nonexistent Folder
	 * 
	 * Test construction with a non existing folder.
	 */
	function testConstructNonexistentFolder() {
		$this->expectException(InvalidArgumentException::class);
		new ComponentsAvailable(__DIR__."/libs/");
	}
	
	/*
	 * Test has component
	 * 
	 * Test if a certain component is known to ComponentsAvailable or not.
	 */
	function testHasComponent() {
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$this->assertEquals(TRUE, $ca->hasComponent("Letters"));
		$this->assertEquals(FALSE, $ca->hasComponent("Lettters"));
	}
	
	/**
	 * Test get Available
	 * 
	 * Test if getComponent returns the path to a known component.
	 */
	function testGetAvailable() {
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$this->assertEquals(__DIR__."/lib01/Letters.php", $ca->getComponent("Letters"));
		$this->assertEquals(__DIR__."/lib01/Character.php", $ca->getComponent("Character"));
	}

	/**
	 * Test get Unavailable
	 * 
	 * Test if getComponent throws an Exception if the component is not known.
	 */
	function testUnavailable() {
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$this->expectException(Exception::class);
		$this->expectExceptionMessage("component Squids not known");
		$ca->getComponent("Squids");
	}

	/**
	 * Test Additional Directory
	 * 
	 * Test if another directory can be added.
	 */
	function testAdditionalDirectory() {
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$ca->addDirectory(__DIR__."/lib02/");
		$this->assertEquals(__DIR__."/lib01/Letters.php", $ca->getComponent("Letters"));
		$this->assertEquals(__DIR__."/lib02/Digits.php", $ca->getComponent("Digits"));
	}

}
