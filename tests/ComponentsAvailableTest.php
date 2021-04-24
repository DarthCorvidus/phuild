<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
class ComponentsAvailableTest extends TestCase {
	/**
	 * Test extract component
	 * 
	 * Extracts a component definition out of a file that contains one
	 * definition.
	 */
	function testExtractComponent() {
		$expect = array();
		$expect[] = "Character";
		$this->assertEquals($expect, ComponentsAvailable::extractComponents(__DIR__."/lib01/Character.php"));
	}

	/**
	 * Test extract components
	 * 
	 * Test to extract multiple component definitions out of one file.
	 */
	function testExtractComponents() {
		$expect = array();
		$expect[] = "Animal";
		$expect[] = "Mammal";
		$expect[] = "Dog";
		$this->assertEquals($expect, ComponentsAvailable::extractComponents(__DIR__."/lib02/Animal.php"));
	}
	
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
	
	/**
	 * Test Add Single File
	 * 
	 * Test to add a single file
	 */
	function testAddSingleFile() {
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$ca->addFile(__DIR__."/lib02/Digits.php");
		$this->assertEquals(__DIR__."/lib01/Letters.php", $ca->getComponent("Letters"));
		$this->assertEquals(__DIR__."/lib02/Digits.php", $ca->getComponent("Digits"));
	}
	
	function testGetComponents() {
		$expected = array();
		$expected["Character "] = __DIR__."/lib01/Character.php";
		$expected["Letters"] = __DIR__."/lib01/Letters.php";
		$expected["Numbers"] = __DIR__."/lib02/Digits.php";
		$expected["Animal"] = __DIR__."/lib02/Animal.php";
		$expected["Mammal"] = __DIR__."/lib02/Animal.php";
		$expected["Dog"] = __DIR__."/lib02/Animal.php";
		sort($expected);
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$ca->addDirectory(__DIR__."/lib02/");
		$this->assertEquals($expected, $ca->getComponents());
	}

}
