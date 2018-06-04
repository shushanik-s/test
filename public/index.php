<?php

function __autoload($classname) {
    $filename = "../core/". $classname .".php";
    include_once($filename);
}
$query = rtrim($_SERVER['QUERY_STRING'],'/');
Router::add("^$", ['controller' => "Main", 'action' => 'index']);
Router::add('^(?P<controller>[a-z-]+)/?(?P<action>[a-z-]+)?$');

mb_parse_str('email=kehaovista@qq.com&city=shanghai&job=Phper', $str);
print_r($str);
?>