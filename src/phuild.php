#!/usr/bin/php
<?php
/*
 * We need to prevent that ComponentsNeeded tries to load classes which were
 * already declared by PHP. However, we also need to prevent that
 * ComponentsNeeded doesn't load classes which were declared within the script
 * itself! Therefore, we get all of PHP's classes & interfaces at the very
 * beginning of the script and use it for further reference on which classes
 * should be ignored or not.
 */
$ignore = array_merge(get_declared_classes(), get_declared_interfaces());
/**
 * We of course add self and parent, 
 */
$ignore[] = "self";
$ignore[] = "parent";
require_once __DIR__.'/include/local/ComponentsAvailable.php';
require_once __DIR__.'/include/local/ComponentsNeeded.php';
require_once __DIR__.'/include/local/Main.php';

try {
	$main = new Main($argv, $ignore);
	$main->run();
} catch (Exception $ex) {
	echo $ex->getMessage().PHP_EOL;
}