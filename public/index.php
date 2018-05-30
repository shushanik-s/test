<?php

$query = rtrim($_SERVER['QUERY_STRING'],'/');
require "../core/Router.php";

Router::add("^$", ['controller' => "Main", 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

$routes = Router::getRoutes();
echo "<pre>".print_r($routes, true)."</pre>";

Router::dispatch($query);

?>