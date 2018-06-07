<?php

class Router
{
    public static $methods = ['GET', 'POST', 'PUT', 'DELETE'];
    protected $routes = [];
    protected static $validators = [];

    public static function get($uri, $action = null)
    {
        return self::add('GET', $uri, $action);
    }

    public static function post($uri, $action = null)
    {
        return self::add('POST', $uri, $action);
    }

    public static function put($uri, $action = null)
    {
        return self::add('PUT', $uri, $action);
    }

    public static function delete($uri, $action = null)
    {
        return self::add('DELETE', $uri, $action);
    }

    protected static function add($method, $uri, $action)
    {
        $route = [
            'uri' => $uri,
            'method' => $method,
            'action' => $action
        ];

        return $routes[] = $route;
    }

    public function match(Request $request)
    {
        foreach ($this->getValidators() as $validator) {
            if (! $validator->matches($this, $request)) {
                return false;
            }
        }

        return true;
    }

    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }

        return static::$validators = [
            new UriValidator, new MethodValidator
        ];
    }




}