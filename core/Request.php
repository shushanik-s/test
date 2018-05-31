<?php

class Request
{
    public $request;
    public $query;
    public $files;
    public $headers;
    public $server;
    public $method;
    public $parameters;

    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getRealMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function header($key = null, $default = null) {
        return $this->retrieveItem('headers', $key, $default);
    }

    protected function retrieveItem($source, $key, $default) {
        if (is_null($key)) {
            return $this->$source->all();
        }

        return $this->$source->get($key, $default);
    }

    public function hasHeader($key) {
        return ! is_null($this->header($key));
    }


}