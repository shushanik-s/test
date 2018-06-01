<?php
include 'Parameter.php';
include 'Header.php';
include 'functions.php';
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

    public function __construct(array $query = array(), array $request = array(), $content = null)
    {
        $this->initialize($query, $request, $content);
    }

    private function initialize(array $query = array(), array $request = array(), $content = null)
    {
        $this->request = new Parameter($request);
        $this->query = new Parameter($query);
        $this->headers = new Header();
        $this->content = $content;
        $this->method = null;
    }

    public function  all() {
        return $this->request->all();
    }

    public function header($key = null, $default = null)
    {
        if (is_null($key)) {
            return $default ? $this->headers[$default] : $this->headers->all();
        }
        return $this->headers[$key];
    }

    public function hasHeader($key)
    {
        return ! is_null($this->header($key));
    }

    public function getMethod()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD']);

        return $this->method;
    }

    public function getRealMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function input($key = null, $default = null)
    {
        return data_get($this->request, $key, $default);
    }
}