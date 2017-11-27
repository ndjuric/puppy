<?php

/**
 * Logger - simple logging class
 */
class Logger
{
    const colors = [
        'ok' => "\033[32;1m",
        'log' => "\033[37;1m",
        'info' => "\033[36;2m",
        'error' => "\033[41;37m",
        'warning' => "\033[48;5;202m",
        'debug' => "\033[100;97m",
        'reset_color' => "\033[0m",
    ];
    private $log_to_file = false;
    private $log_to_file_var_dump = false;
    private $log_to_stdout = true;
    private $logfile;

    public function __construct()
    {
        $this->logfile = '/tmp/debug.log';
    }

    /**
     * Sets a custom log file path
     *
     * @param $logfile string
     */
    public function setLogfile($logfile)
    {
        $this->logfile = $logfile;
    }

    /**
     * Set log_to_file state to true or false
     *
     * @param $log_to_file boolean
     */
    public function logToFile($log_to_file)
    {
        if (is_bool($log_to_file)) {
            $this->log_to_file = $log_to_file;
        }
    }

    /**
     * Set lot_to_file_var_dump state to true or false
     *
     * @param $log_to_file_var_dump boolean
     */
    public function logToFileVarDump($log_to_file_var_dump)
    {
        if (is_bool($log_to_file_var_dump)) {
            $this->log_to_file_var_dump = $log_to_file_var_dump;
        }
    }

    /**
     * Set log_to_stdout state to true or false
     *
     * @param $log_to_stdout boolean
     */
    public function logToStdout($log_to_stdout)
    {
        if (is_bool($log_to_stdout)) {
            $this->log_to_stdout = $log_to_stdout;
        }
    }

    /**
     * This method logs file
     *
     * @param $log_str string
     */
    public function fileLogger($log_str)
    {
        $dts = date("d.m.Y H:i:s", time());
        $log_str = $dts . ": {$log_str}\r\n";
        if (($fp = fopen($this->logfile, 'a+')) !== false) {
            fputs($fp, $log_str);
            fclose($fp);
        }
    }

    /**
     * This method logs var_dump to file
     *
     * @param $log_str string
     */
    public function varDumpFileLogger($log_str)
    {
        ob_start();
        var_dump($log_str);
        $result = ob_get_clean();

        $dts = date("d.m.Y H:i:s", time());
        $log_str = $dts . ": {$result}\r\n";

        if (($fp = fopen($this->logfile, 'a+')) !== false) {
            fputs($fp, $log_str);
            fclose($fp);
        }
    }

    /**
     * This method logs to stdout
     *
     * @param $log_str mixed
     * @param log_type
     */
    private function stdoutLogger($log_str, $log_type)
    {
        $dts = date("d.m.Y H:i:s", time());

        $dts = $this::colors['log'] . "[ " . $dts . " ]: " . $this::colors['reset_color'];
        $str = $this::colors[$log_type] . $log_str . $this::colors['reset_color'];

        $colored_str = $dts . $str;

        echo $colored_str . PHP_EOL;
    }

    /**
     * This method encapsulates both stdout and file logging methods
     *
     * @param $log_str string
     * @param log_type
     */
    public function log($log_str, $log_type)
    {
        if ($this->log_to_stdout) {
            $this->stdoutLogger($log_str, $log_type);
        }

        if ($this->log_to_file && getenv('APP_ENV') !== 'PROD') {
            $this->fileLogger($log_str);
        }

        if ($this->log_to_file_var_dump && getenv('APP_ENV') !== 'PROD') {
            $this->varDumpFileLogger($log_str);
        }
    }

}