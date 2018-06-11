<?php

function __autoload($classname) {
    $filename = '../core/'.$classname .'.php';
    include_once($filename);
}

$base = Database::instance();

$users = $base->select();
print_r($users);
?>