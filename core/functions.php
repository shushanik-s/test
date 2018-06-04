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

function normalizeQueryString($qs)
{
    if ('' == $qs) {
        return '';
    }

    $parts = array();
    $order = array();

    foreach (explode('&', $qs) as $param) {
        if ('' === $param || '=' === $param[0]) {
            // Ignore useless delimiters, e.g. "x=y&".
            // Also ignore pairs with empty key, even if there was a value, e.g. "=value", as such nameless values cannot be retrieved anyway.
            // PHP also does not include them when building _GET.
            continue;
        }

        $keyValuePair = explode('=', $param, 2);

        // GET parameters, that are submitted from a HTML form, encode spaces as "+" by default (as defined in enctype application/x-www-form-urlencoded).
        // PHP also converts "+" to spaces when filling the global _GET or when using the function parse_str. This is why we use urldecode and then normalize to
        // RFC 3986 with rawurlencode.
        $parts[] = isset($keyValuePair[1]) ?
            rawurlencode(urldecode($keyValuePair[0])).'='.rawurlencode(urldecode($keyValuePair[1])) :
            rawurlencode(urldecode($keyValuePair[0]));
        $order[] = urldecode($keyValuePair[0]);
    }

    array_multisort($order, SORT_ASC, $parts);

    return implode('&', $parts);
}