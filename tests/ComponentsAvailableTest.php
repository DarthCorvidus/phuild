<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AssertTest
 *
 * @author hm
 */
class ComponentsAvailableTest extends TestCase {
	function testConstruct() {
		$ca = new ComponentsAvailable(__DIR__."/lib01/");
		$this->assertInstanceOf("ComponentsAvailable", $ca);
	}
	
	function testConstructNonexistentFolder() {
		$this->expectException(InvalidArgumentException::class);
		new ComponentsAvailable(__DIR__."/libs/");
	}
}
