<?php

function data_get($target, $key, $default) {
    if (is_null($key)) {
        return $target;
    }

    $key = is_array($key) ? $key : explode('.', $key);

    while (! is_null($segment = array_shift($key))) {
        if (array_key_exists($target, $segment)) {
            $target = $target[$segment];
        } elseif (is_object($target) && isset($target->$segment)) {
            $target = $target->$segment;
        } else {
            return $default;
        }
    }

    return $target;
}