<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */

/**
 * ImportJob
 * 
 * ImportModel for Import to import array from build configuration.
 */
class ImportJob implements ImportModel {
	private $scalar;
	private $scalarList;
	function __construct() {
		$this->scalar["name"] = new ScalarGeneric();
		$this->scalar["name"]->setMandatory();
		
		$this->scalar["source"] = new ScalarGeneric();
		$this->scalar["source"]->setMandatory();
		
		$this->scalar["target"] = new ScalarGeneric();
		$this->scalar["target"]->setMandatory();
		
		$this->scalarList["includes"] = new ScalarGeneric();
		$this->scalarList["includes"]->setMandatory();
	}
	
	public function getImportListModel($name): \ImportModel {
		
	}

	public function getImportListNames(): array {
		return array();
	}

	public function getImportModel($name): \ImportModel {
		
	}

	public function getImportNames(): array {
		return array();
	}

	public function getScalarListModel($name): \ScalarModel {
		return $this->scalarList[$name];
	}

	public function getScalarListNames(): array {
		return array_keys($this->scalarList);
	}

	public function getScalarModel($name): \ScalarModel {
		return $this->scalar[$name];
	}

	public function getScalarNames(): array {
		return array_keys($this->scalar);
	}

}