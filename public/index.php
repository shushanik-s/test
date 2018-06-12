<?php

function __autoload($classname) {
    $filename = '../core/'.$classname .'.php';
    include_once($filename);
}

$base = Database::instance();
$mysqli = $base->getConnection();
$q = $base->users->where('id', '>', 1)->select(['id', 'name']);

print_r($q);

?>