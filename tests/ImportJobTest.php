<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
class ImportJobTest extends TestCase {
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
	 * Test valid import
	 * 
	 * Test to import a valid array
	 */
	function testValidImport() {
		$model = new ImportJob();
		$import = new Import($this->getDefaultArray(), $model);
		$this->assertEquals($this->getDefaultArray(), $import->getArray());
	}
	
	/**
	 * Test empty array
	 * 
	 * Test to import an empty array
	 */
	function testEmptyArray() {
		$model = new ImportJob();
		$import = new Import(array(), $model);
		$this->expectException(ImportException::class);
		$import->getArray();
	}
	
	/**
	 * Test missing name
	 * 
	 * Test to import an array with a missing name
	 */
	function testMissingName() {
		$array = $this->getDefaultArray();
		unset($array["name"]);
		$model = new ImportJob();
		$import = new Import($array, $model);
		$this->expectException(ImportException::class);
		$this->expectExceptionMessage("[\"name\"] is missing from array");
		$import->getArray();
	}

	/**
	 * Test missing source
	 * 
	 * Test to import an array with a missing source
	 */
	function testMissingSource() {
		$array = $this->getDefaultArray();
		unset($array["source"]);
		$model = new ImportJob();
		$import = new Import($array, $model);
		$this->expectException(ImportException::class);
		$this->expectExceptionMessage("[\"source\"] is missing from array");
		$import->getArray();
	}

	/**
	 * Test missing target
	 * 
	 * Test to import an array with a missing target
	 */
	function testMissingTarget() {
		$array = $this->getDefaultArray();
		unset($array["target"]);
		$model = new ImportJob();
		$import = new Import($array, $model);
		$this->expectException(ImportException::class);
		$this->expectExceptionMessage("[\"target\"] is missing from array");
		$import->getArray();
	}

	/**
	 * Test missing includes
	 * 
	 * Test to import an array with a missing list of includes
	 */
	function testMissingIncludes() {
		$array = $this->getDefaultArray();
		unset($array["includes"]);
		$model = new ImportJob();
		$import = new Import($array, $model);
		$this->expectException(ImportException::class);
		$this->expectExceptionMessage("[\"includes\"][] is mandatory, needs to contain at least one value");
		$import->getArray();
	}

}
