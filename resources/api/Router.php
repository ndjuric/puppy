<?php

class Router extends API
{
    private $logger;

    public function __construct($request, $origin)
    {
        parent::__construct($request);
        $this->logger = new Logger();
        $this->logger->logToFile(false);
        $this->logger->logToFileVarDump(true);
        $this->logger->setLogfile('/tmp/router.log');
        $this->logger->logToStdout(false);
        // save tokens to memcached || ddos protection || access control per ip
    }

    /* endpoint: http://puppy.local/api/test_endpoint?id=some_id */
    protected function test_endpoint($body, $args)
    {
        switch ($this->method) {
            case "GET":
                return ['get'];
            case "POST":
                return ['post'];
            default:
                return $this->invalidMethod();
        }
    }
}
