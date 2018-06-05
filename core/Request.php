<?php
//namespace core;
//
//use Header;
//use Parameter;

include 'functions.php';

class Request
{
    protected $parameters;
    protected $request;
    protected $query;
    public $headers;
    protected $content;
    protected $requestUri;
    protected $baseUrl;
    protected $method;
    protected $rawData;
    protected static $httpMethodParameterOverride = false;

    public function __construct(array $query = array(), array $request = array(), $content = null)
    {
        $this->initialize($query, $request, $content);
    }

    private function initialize(array $query = array(), array $request = array(), $content = null)
    {
        $this->request = $_REQUEST;
        $this->query   = new Parameter($query);
        $this->headers = new Header();
        $this->content = $content;
        $this->method  = null;
    }

    public function all()
    {
        return $this->request;
    }

    public function header($key = null, $default = null)
    {
        if (is_null($key)) {
            return $default ? $this->headers->headers[$default] : $this->headers;
        }
        return $this->headers->headers[$key];
    }

    public function hasHeader($key)
    {
        return !is_null($this->header($key));
    }

    public function getMethod()
    {
        if (null === $this->method) {
            $this->method = $_SERVER['REQUEST_METHOD'] ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';

            if ('POST' === $this->method) {
                if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) {
                    $this->method = strtoupper($method);
                } elseif (self::$httpMethodParameterOverride) {
                    $this->method = strtoupper($this->request->get('_method', 'POST'));
                }
            }
        }

        return $this->method;
    }

    public function getRealMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }

    public function input($key = null, $default = null)
    {

        return data_get($_REQUEST, $key, $default);
    }

    public static function normalizeQueryString($qs)
    {
        if ('' == $qs) {
            return '';
        }

        $parts = array();
        $order = array();

        foreach (explode('&', $qs) as $param) {
            if ('' === $param || '=' === $param[0]) {
                continue;
            }

            $keyValuePair = explode('=', $param, 2);

            $parts[] = isset($keyValuePair[1]) ?
                rawurlencode(urldecode($keyValuePair[0])) . '=' . rawurlencode(urldecode($keyValuePair[1])) :
                rawurlencode(urldecode($keyValuePair[0]));
            $order[] = urldecode($keyValuePair[0]);
        }

        array_multisort($order, SORT_ASC, $parts);

        return implode('&', $parts);
    }

    public static function enableHttpMethodParameterOverride()
    {
        self::$httpMethodParameterOverride = true;
    }

    public static function getHttpMethodParameterOverride()
    {
        return self::$httpMethodParameterOverride;
    }

    public function getQueryString()
    {
        $qs = static::normalizeQueryString($_SERVER['QUERY_STRING']);

        return '' === $qs ? null : $qs;
    }

    public function contains($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
    public function isJson()
    {
        $needles = ['/json', '+json'];
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($this->header('content-type')[0], $needle) !== false) {
                return true;
            }
        }

        return false;
    }



    public function getContent()
    {
        $this->rawData = file_get_contents('php://input');
        if ($this->header('content-type')[0] == 'application/json') {
            $this->content = array_merge(json_decode($this->rawData), $this->rawData);
        } elseif ($this->header('content-type')[0] == 'application/x-www-form-urlencoded') {
            parse_str(file_get_contents("php://input"), $this->content);
            $this->content = array_merge(json_decode($this->rawData), $this->rawData);
        } elseif (strpos($this->header('content-type')[0], 'multipart/form-data') !== false) {
            if($this->rawData) {
                $this->content = $this->parseFormData($this->rawData);
            }
        }

        return $this->content;
    }

    public function parseFormData($formData)
    {
        $endOfFirstLine = strpos($formData, "\r\n");
        $boundary = substr($formData, 0, $endOfFirstLine);
        // Split form-data into each entry
        $parts = explode($boundary, $formData);
        $return = [];
        // Remove first and last (null) entries
        array_shift($parts);
        array_pop($parts);
        foreach ($parts as $part) {
            $endOfHead = strpos($part, "\r\n\r\n");
            $startOfBody = $endOfHead + 4;
            $head = substr($part, 2, $endOfHead - 2);
            $body = substr($part, $startOfBody, -2);
            $headerParts = preg_split('#; |\r\n#', $head);
            $key = null;
            $thisHeader = [];
            // Parse the mini headers,
            // obtain the key
            foreach ($headerParts as $headerPart) {
                if (preg_match('#(.*)(=|: )(.*)#', $headerPart, $keyVal)) {
                    if ($keyVal[1] == "name") $key = substr($keyVal[3], 1, -1);
                    else {
                        if($keyVal[2] == "="){
                            $thisHeader[$keyVal[1]] = substr($keyVal[3], 1, -1);
                        }else{
                            $thisHeader[$keyVal[1]] = $keyVal[3];
                        }
                    }
                }
            }

            if (isset($thisHeader['filename'])) {
                $filename = tempnam(sys_get_temp_dir(), "php");
                file_put_contents($filename, $body);
                $return[$key] = [
                    "name" => $thisHeader['filename'],
                    "type" => $thisHeader['Content-Type'],
                    "tmp_name" => $filename,
                    "error" => 0,
                ];
            } else {
                $return[$key] = $body;
            }
            $return[$key] = $body;
        }
        return $return;
    }
}