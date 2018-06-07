<?php

class RouteCollection
{
    protected $routes     = [];
    protected $allRoutes  = [];
    protected $nameList   = [];
    protected $actionList = [];

    public function add(Route $route)
    {
        $this->addToCollections($route);

        return $route;
    }

    protected function addToCollections($route)
    {
        $domainAndUri = $route->domain().$route->uri();

        foreach ($route->methods() as $method) {
            $this->routes[$method][$domainAndUri] = $route;
        }

        $this->allRoutes[$method.$domainAndUri] = $route;
    }
}