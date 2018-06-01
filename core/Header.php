<?php

class Header
{
    protected $headers = [];

    public function __construct()
    {
        $headers = getallheaders();

        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    public function all()
    {
        return $this->headers;
    }

    public function get($key, $default = null)
    {
        $headers = $this->all();

        $key = str_replace('_', '-', strtolower($key));

        if (!array_key_exists($key, $headers)) {
            if (null === $default) {
                return array();
            }

            return array($default);
        }

        return $headers[$key];
    }

    public function set($key, $values)
    {
        $key = str_replace('_', '-', strtolower($key));

        if (is_array($values)) {
            $values = array_values($values);
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        } else {
            $this->headers[$key][] = $values;
        }
    }

    public function count()
    {
        return count($this->headers);
    }
}