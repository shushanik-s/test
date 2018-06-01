<?php
include 'Parameter.php';
include 'Header.php';
class Request
{
    protected $parameters;
    protected $request;
    protected $query;
    protected $headers;
    protected $content;
    protected $requestUri;
    protected $baseUrl;
    protected $method;
    protected $server;

    public function __construct(array $query = array(), array $request = array(), array $attributes = array(), array $cookies = array(), array $files = array(), array $server = array(), $content = null)
    {
        $this->initialize($query, $request, $content);
    }

    private function initialize(array $query = array(), array $request = array(), $content = null)
    {
        $this->request = new Parameter($request);
        $this->query = new Parameter($query);
        $this->headers = new Header();
        $this->content = $content;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->method = null;
    }

    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getRealMethod() {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function header($key = null, $default = null) {
        if (is_null($key)) {
            return $default ? $this->headers[$default] : $this->headers->all();
        }
        return $this->headers[$key];
    }

    public function hasHeader($key) {
        return ! is_null($this->header($key));
    }
}