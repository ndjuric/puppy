<?php
ini_set('zlib.output_compression', 4096);

abstract class API
{

    protected $request = '';

    protected $method = '';

    protected $endpoint = '';

    protected $action = '';

    protected $args = [];

    protected $response_table = [
        'json' => 'Response',
        'html' => 'Render',
    ];

    protected $response_type = 'json';

    protected $response_function = 'Response';


    /**
     * API constructor.
     *
     * @param $request string - request uri from which the endpoints and args are extracted.
     * @param $type string - can be 'json' or 'render', defines whether API response will be JSON or HTML.
     */
    public function __construct($request, $type = 'json')
    {
        if (array_key_exists($type, $this->response_table)) {
            $this->response_type = $type;
            $this->response_function = $this->response_table[$type];
        }
        $this->setHeader();

        // format: endpoint/_name/action_name
        // endpoint is the name of a method in a class that inherits this class
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (empty($this->endpoint)) {
            $this->endpoint = 'index';
        }
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->action = array_shift($this->args);
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Set HTTP header according to the rendering type provided in the constructor.
     */
    private function setHeader()
    {
        if ($this->response_type === 'json') {
            header("Content-Type: application/json");
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST, GET");
            header("Expires: Sat, 01 Jan 1970 00:00:00 GMT");
            header("Pragma: no-cache");
        }
    }

    /**
     * Performs request processing, http verb/method lookup, cleaning.
     *
     * @return string output of method named like the endpoint
     * if there is no endpoint, returns invalid endpoint message
     * if http method/verb is not supported, return invalid method message
     */
    public function Process()
    {
        switch ($this->method) {
            case 'GET':
                $this->request = $this->Clean($_GET);
                break;
            case 'POST':
                if (array_key_exists('CONTENT_TYPE', $_SERVER) && $_SERVER['CONTENT_TYPE'] === "application/json") {
                    $this->request = $this->getJson();
                } else {
                    $this->request = $_POST;
                }

                if (is_array($this->request)) {
                    $this->request = $this->Clean($this->request);
                } else {
                    $this->request = [];
                }

                break;
            default:
                return $this->invalidMethod();
                break;
        }

        $response = ['status' => 'Invalid API endpoint'];
        $status_code = 404;
        if ((int)method_exists($this, $this->endpoint) > 0) {
            $status_code = 200;
            $response = $this->{$this->endpoint}($this->request, $this->args);

            if (is_array($response)) {
                if (array_key_exists('status_code',
                        $response) && is_numeric($response['status_code'])
                ) {
                    $status_code = $response['status_code'];
                    unset($response['status_code']);
                }
            }
        }

        return $this->{$this->response_function}($response, $status_code);
    }

    /**
     * @return mixed - hashmap/associative array with json data, or false if invalid json is inputted
     */
    private function getJson()
    {
        $input_json = file_get_contents('php://input');
        return json_decode($input_json, true);
    }

    /**
     * @return string - Invalid method response
     */
    protected function invalidMethod()
    {
        return $this->Response(['status' => $this->Status(405)], 405);
    }

    /**
     * @param $data - Response data
     * @param int $code - HTTP status code
     *
     * @return string - JSON encoded response data and HTTP status code
     */
    protected function Response($data, $code = 200)
    {
        header("HTTP/1.1 " . $code . " " . $this->Status($code));

        $data = $this->Clean($data);

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * @param $data - Response data
     * @param int $code - HTTP status code
     *
     * @return string - json encoded response data and http status code in http
     */
    protected function Render($data, $code = 200)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }
        header("HTTP/1.1 " . $code . " " . $this->Status($code));
        $data = str_replace("\r", ' ', $data);
        $data = str_replace("\n", ' ', $data);
        $data = str_replace("\t", ' ', $data);
        return $data;
    }

    /**
     * @param $data array|string - data to be cleaned and stripped of potential malicious characters
     *
     * @return array|string - cleaned input
     */
    private function Clean($data)
    {
        $clean_input = [];
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->Clean($v);
            }
        } else {

            if (is_bool($data)) {
                return $data;
            }
            if (is_float($data)) {
                return $data;
            }
            if (is_int($data)) {
                return $data;
            }

            $clean_input = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');

            $clean_input = trim($clean_input);
        }

        return $clean_input;
    }

    /**
     * @param $code - integer corresponding to an index of predefined status
     *   array
     *
     * @return mixed - either returns a status message describing it in more
     *   details, or 'Not Implemented'
     */
    private function Status($code)
    {
        $status = [
            200 => 'OK',
            301 => 'Moved Permanently',
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            503 => 'Service Unavailable',
        ];
        return ($status[$code]) ? $status[$code] : $status[501];
    }
}
