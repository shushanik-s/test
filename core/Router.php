<?php

class Router
{
    private static $routes = [];
    public static $validators = [];

    public static function get($uri, $action = null)
    {
        self::$routes[] = self::add('GET', $uri, $action);
    }

    public static function post($uri, $action = null)
    {
        self::$routes[] = self::add('POST', $uri, $action);
    }

    public static function put($uri, $action = null)
    {
        self::$routes[] =  self::add('PUT', $uri, $action);
    }

    public static function delete($uri, $action = null)
    {
        self::$routes[] = self::add('DELETE', $uri, $action);
    }

    public static function add($method, $uri, $action)
    {
        $route = [
            'uri' => $uri,
            'method' => $method,
            'action' => $action
        ];

        if (strpos($uri, '{') !== false) {
            $route['regex'] = static::generateRegex($uri);
        }

        return $routes[] = $route;
    }

    public static function match(Request $request)
    {
        foreach (self::$routes as $route) {
            echo "aa";
            $valid = true;
            foreach (static::getValidators() as $validator) {
                echo "aaa";
                if (!$validator->matches($route, $request)) {
                    $valid = false;
                    break;
                }
            }

            if($valid) {
                return $route;
            }
        }
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

    private static function generateRegex($uri) {
        $segments = explode('/', $uri);
        $regex = [];

        foreach ($segments as $segment) {
            $pattern = '~\{(\w+)\}~';
            if (preg_match($pattern, $segment, $matches)) {
                $segment = $matches[1];
                $regex[] ="(?P<$segment>[^\/]+)";
            } else {
                $regex[] = $segment;
            }
        }
        $regex = "/^".implode('/', $regex)."$/";

        return $regex;
    }




}