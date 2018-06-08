<?php

function __autoload($classname) {
    $filename = '../core/'.$classname .'.php';
    include_once($filename);
}


$request = new Request();

Router::get("posts/{id}/comments/{name}", "aaa");
Router::post("posts/{id}", 'aaa');

Router::match($request);

?>