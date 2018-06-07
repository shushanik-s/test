<?php

class Router
{
    protected $routes = [];
    protected $namedRoutes = [];
    protected $currentRequest;
    public static $verbs = ['GET', 'POST', 'PUT', 'DELETE'];

    public function __construct()
    {
        $this->routes = new RouteCollection;
    }

    public function get($uri, $action = null)
    {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    public function post($uri, $action = null)
    {
        return $this->addRoute('POST', $uri, $action);
    }

    public function put($uri, $action = null)
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    public function delete($uri, $action = null)
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    protected function addRoute($methods, $uri, $action)
    {
        return $this->routes->add($this->createRoute($methods, $uri, $action));
    }

    protected function createRoute($methods, $uri, $action)
    {
        if ($this->actionReferencesController($action)) {
            $action = $this->convertToControllerAction($action);
        }

        $route = $this->newRoute(
            $methods, $this->prefix($uri), $action
        );

        return $route;
    }

    protected function actionReferencesController($action)
    {
        return is_string($action) || (isset($action['uses']) && is_string($action['uses']));
    }

    protected function convertToControllerAction($action)
    {
        if (is_string($action)) {
            $action = ['uses' => $action];
        }

        $action['controller'] = $action['uses'];

        return $action;
    }

    public function match($methods, $uri, $action = null)
    {
        return $this->addRoute(array_map('strtoupper', (array) $methods), $uri, $action);
    }

    protected function newRoute($methods, $uri, $action)
    {
        return (new Route($methods, $uri, $action))
            ->setRouter($this);
    }

}