<?php

function __autoload($classname) {
    $filename = '../core/'.$classname .'.php';
    include_once($filename);
}


$request = new Request();

print_r($request->all());

?>