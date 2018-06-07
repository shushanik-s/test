<?php

class MethodValidator
{
    public function matches($route, Request $request)
    {
        return in_array($request->getMethod(), $route->methods);
    }
}