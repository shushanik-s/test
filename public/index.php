<?php
require "../core/Router.php";
require "../core/Request.php";
require "../app/Controllers/Main.php";
require "../app/Controllers/Posts.php";

$query = rtrim($_SERVER['QUERY_STRING'],'/');

Router::add("^$", ['controller' => "Main", 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

$request = new Request();
$key = 'host';

echo '<pre>',print_r($request->header()),'</pre>';
?>