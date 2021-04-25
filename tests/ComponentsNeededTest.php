<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
class ComponentsNeededTest extends TestCase {
	function setUp() {
		$this->ca = new ComponentsAvailable(__DIR__."/lib01/");
		$this->ca->addDirectory(__DIR__."/lib02/");
	}
	
	/**
	 * Test Extract Needed
	 * 
	 * Extract necessary classes to run dog.php
	 */
	function testExtractNeeded() {
		$needed = ComponentsNeeded::extractNeeded(__DIR__."/example/dog.php");
		$this->assertEquals(array("Dog"), $needed);
		$needed = ComponentsNeeded::extractNeeded(__DIR__."/example/characters.php");
		$this->assertEquals(array("Digits", "Letters", "Stringtools"), $needed);

	}

	/**
	 * Test extract illegal instantiation
	 * 
	 * In PHP, you can use a variable instead of a class name on instantiation.
	 * As the variable's content is only available on runtime, there is no way
	 * for Phuild to know which class is needed.
	 */
	function testExtractIllegalInstantiation() {
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage("class name \$classname in ".__DIR__."/example/illegalNew.php line 3 contains a variable.");
		ComponentsNeeded::extractNeeded(__DIR__."/example/illegalNew.php");
	}

	/**
	 * Test extract illegal static call
	 * 
	 * Same as above, only for static call/property access.
	 */
	function testExtractIllegalStaticCall() {
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage("class name \$classname in ".__DIR__."/example/illegalStatic.php line 3 contains a variable.");
		ComponentsNeeded::extractNeeded(__DIR__."/example/illegalStatic.php");
	}

	/**
	 * Test Ignore Include Block
	 * 
	 * Any code between #Include and #/Include has to be ignored
	 */
	function testIgnoreIncludeBlock() {
		$needed = ComponentsNeeded::extractNeeded(__DIR__."/example/include.php");
		#$string = file_get_contents(__DIR__."/example/include.php");
		#print_r(token_get_all($string));
		$this->assertEquals(array("Dog"), $needed);
	}
	
	/**
	 * Test construct
	 * 
	 * Just construct an instance of ComponentsNeeded
	 */
	function testConstruct() {
		$cn = new ComponentsNeeded(__DIR__."/example/dog.php", $this->ca, array());
		$this->assertInstanceOf(ComponentsNeeded::class, $cn);
	}
	
	/**
	 * Test construct nonexistent file
	 * 
	 * Try to construct on a non-existent file.
	 */
	function testConstructNonexistentFile() {
		$this->expectException(InvalidArgumentException::class);
		$cn = new ComponentsNeeded(__DIR__."/example/dogs.php", $this->ca, array());
	}
	
	/**
	 * Test get classes one file
	 * 
	 * Test get classes if classes are all from one file.
	 */
	function testGetClassesOneFile() {
		$expected = array("Mammal", "Animal", "Dog");
		$cn = new ComponentsNeeded(__DIR__."/example/dog.php", $this->ca, array());
		$this->assertEquals($expected, $cn->getClasses());
	}
	
	/**
	 * Test get classes multiple files
	 * 
	 * Test getClasses if classes are distributed over several files.
	 */
	function testGetClassesMultipleFiles() {
		$expected = array("Character", "Digits", "Letters", "Stringtools");
		$cn = new ComponentsNeeded(__DIR__."/example/characters.php", $this->ca, array());
		$this->assertEquals($expected, $cn->getClasses());
	}

}
