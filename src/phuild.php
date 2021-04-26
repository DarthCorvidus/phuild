#!/usr/bin/php
<?php
/**
 * @copyright (c) 2019, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <floss@vm01.telton.de>
 * @license GPLv3
 */
/*
 * We need to prevent that ComponentsNeeded tries to load classes which were
 * already declared by PHP. However, we also need to prevent that
 * ComponentsNeeded doesn't load classes which were declared within the script
 * itself! Therefore, we get all of PHP's classes & interfaces at the very
 * beginning of the script and use it for further reference on which classes
 * should be ignored or not.
 * Also, we use parseFile to search which classes are defined within ourselves,
 * otherwise we couldn't use a version of phuild.php generated by phuild with
 * sources included. 
 */
$declared = array_merge(get_declared_classes(), get_declared_interfaces());
function parseFile($file):array {
	$string = file_get_contents($file);
	$tokens = token_get_all($string);
	$names = array();
	$interesting = array(T_CLASS, T_INTERFACE);
	foreach($tokens as $key => $value) {
		if(!is_array($value)) {
			continue;
		}
		if(!in_array($value[0], $interesting)) {
			continue;
		}
		if(!isset($tokens[$key+1]) || !isset($tokens[$key+2])) {
			continue;
		}
		if($tokens[$key+1][0]!=T_WHITESPACE) {
			continue;
		}
		$names[] = $tokens[$key+2][1];
	}
return $names;
}
$decSelf = parseFile(__FILE__);
$ignore = array();
foreach($declared as $value) {
	if(in_array($value, $decSelf)) {
		continue;
	}
	$ignore[] = $value;
}
$ignore[] = "self";
$ignore[] = "parent";

#Include
require_once __DIR__.'/../vendor/autoload.php';
#/Include

try {
	$main = new Main($argv, $ignore);
	$main->run();
} catch (ArgvException $ex) {
	echo $ex->getMessage().PHP_EOL;
}