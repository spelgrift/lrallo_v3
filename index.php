<?php

require 'config.php';

function __autoload($class){
	require LIBS . $class. ".php";
}

$bootstrap =  new Bootstrap();
$bootstrap->init();

?>


