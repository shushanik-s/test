<?php
require "../core/Router.php";
require "../app/Controllers/Main.php";
require "../app/Controllers/Posts.php";

$query = rtrim($_SERVER['QUERY_STRING'],'/');

Router::add("^$", ['controller' => "Main", 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

$routes = Router::getRoutes();
echo "<pre>".print_r($routes, true)."</pre>";

Router::dispatch($query);

?>