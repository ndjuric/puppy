<?php

class APIFactory
{

    private $api_origin;
    private $allowed_origins;
    private $class_call_map;

    public function __construct()
    {
        $this->allowed_origins = Config::$API['ALLOWED_ORIGINS'];
        $this->class_call_map = Config::$API['CLASS_CALL_MAP'];
        $this->api_origin = $this->getOrigin();
        $this->Instantiate($this->class_call_map[$this->api_origin]);
    }

    private function getOrigin()
    {
        $api_origin = 'frontend';
        $request_uri = $_SERVER['REQUEST_URI'];

        $uri_parts = explode('?', $request_uri);
        $request_uri = $uri_parts[0];

        if(substr_count($request_uri, '/') > 1) {
            $api_origin = ltrim($request_uri, '/');
            $origin_parts = explode('/', $api_origin);
            $api_origin = array_shift($origin_parts);
        }
        if(!in_array($api_origin, $this->allowed_origins)) {
            $api_origin = 'frontend';
        }
        return $api_origin;
    }

    private function Instantiate($api_class)
    {
        // no http origin header if requests comes from the same server
        if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
            $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
        }

        if (!array_key_exists('request', $_REQUEST)) {
            $_REQUEST['request'] = 'index';
        }

        try {
            $API = new $api_class($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
            echo $API->Process();
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}