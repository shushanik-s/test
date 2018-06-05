<?php

function data_get($target, $key, $default) {
    if (is_null($key)) {
        return $target;
    }

    $key = is_array($key) ? $key : explode('.', $key);

    $segment = array_shift($key);

    while (! is_null(array_shift($key))) {
        if (array_key_exists($segment, $target)) {
            $target = $target['segment'];
        } elseif (is_object($target) && isset($target->$segment)) {
            $target = $target->$segment;
        } else {
            return $default;
        }
    }

    return $target;
}

function normalizeQueryString($qs)
{
    if ('' == $qs) {
        return '';
    }

    $parts = array();
    $order = array();

    foreach (explode('&', $qs) as $param) {
        if ('' === $param || '=' === $param[0]) {
            continue;
        }

        $keyValuePair = explode('=', $param, 2);

        $parts[] = isset($keyValuePair[1]) ?
            rawurlencode(urldecode($keyValuePair[0])).'='.rawurlencode(urldecode($keyValuePair[1])) :
            rawurlencode(urldecode($keyValuePair[0]));
        $order[] = urldecode($keyValuePair[0]);
    }

    array_multisort($order, SORT_ASC, $parts);

    return implode('&', $parts);
}