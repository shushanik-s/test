<?php

class Router
{
    protected $events;
    protected $container;
    protected $routes;
    protected $current;
    protected $currentRequest;
    protected $middleware = [];
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    public function __construct()
    {
        $this->routes = new RouteCollection;
    }
}