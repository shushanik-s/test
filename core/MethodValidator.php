<?php

class MethodValidator
{
    public function matches($route, Request $request)
    {
        return $request->getMethod() == $route['method'];
    }
}