<?php

include "Router.php";
$query = rtrim($_SERVER['QUERY_STRING'],'/');

Router::add("^$", ['controller' => "Main", 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

$request = new Request();
$key = 'host';

echo '<pre>',print_r($request->all()),'</pre>';
?>