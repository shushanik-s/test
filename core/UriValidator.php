<?php

class UriValidator
{
    public function matches($route, Request $request)
    {
        if (isset($route['regex'])) {
            preg_match($route['regex'], $request->requestUri, $matches);
            if ($matches) {
                return true;
            }
        } elseif ($request->requestUri == $route['uri']) {
            return true;
        }
        return false;
    }
}