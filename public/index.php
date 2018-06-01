<?php

//include "../core/Router.php";
//include "../core/Request.php";

function __autoload($classname) {
    $filename = "../core/". $classname .".php";
    include_once($filename);
}

$query = rtrim($_SERVER['QUERY_STRING'],'/');

Router::add("^$", ['controller' => "Main", 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

/** @var object $header */

$header= new Header();
$key = 'host';

echo '<pre>',print_r($header->all()),'</pre>';
?>