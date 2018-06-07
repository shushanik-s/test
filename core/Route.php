<?php

class Route
{
    public $uri;
    public $methods;
    public $action;
    public $controller;
    public $router;
    public $container;
    public $validators;

    public function __construct($methods, $uri, $action)
    {
        $this->uri = $uri;
        $this->methods = (array) $methods;
        $this->action = $this->parseAction($action);

        if (in_array('GET', $this->methods) && ! in_array('HEAD', $this->methods)) {
            $this->methods[] = 'HEAD';
        }
    }

    protected function parseAction($action)
    {
        return parse($this->uri, $action);
    }

    public function parse($uri, $action)
    {
        if (is_null($action)) {
            return ['uses' => "Route for [{$uri}] has no action."];
        }

        if (is_callable($action)) {
            return ['uses' => $action];
        }

        elseif (! isset($action['uses'])) {
            $action['uses'] = static::findCallable($action);
        }

        if (is_string($action['uses']) && ! contains($action['uses'], '@')) {
            $action['uses'] = static::makeInvokable($action['uses']);
        }

        return $action;
    }

    protected static function findCallable(array $action)
    {
        return first($action, function ($value, $key) {
            return is_callable($value) && is_numeric($key);
        });
    }

    protected static function makeInvokable($action)
    {
        if (! method_exists($action, '__invoke')) {
            throw new UnexpectedValueException("Invalid route action: [{$action}].");
        }

        return $action.'@__invoke';
    }

    public function first($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return $default;
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return $default;
    }

    public function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public function setRouter(Router $router)
    {
        $this->router = $router;

        return $this;
    }

    public function domain()
    {
        return isset($this->action['domain'])
            ? str_replace(['http://', 'https://'], '', $this->action['domain']) : null;
    }

    public function methods()
    {
        return $this->methods;
    }
}