<?php
/**
 * Configuration
 */
final class Config
{
    /**
     * @var array - System settings
     */
    static $SYSTEM = [
        'TIMEZONE' => 'Europe/Belgrade',
    ];

    /**
     * @var array - Database information
     */
    static $DB = [
        'HOST' => 'localhost',
        'USER' => 'root',
        'PASS' => '',
        'NAME' => 'test',
    ];

    /**
     * List of exposed API's, map describing which link invokes which routing class
     *
     * @var array
     */
    static $API = [
        'ALLOWED_ORIGINS' => ['frontend', 'api'],
        'CLASS_CALL_MAP' => [
            'frontend' => 'Home',
            'api' => 'Router',
        ],
    ];

    /**
     * Template categorization by directories
     *
     * @var array
     */
    static $ASSETS = [
        'PATH' => __DIR__,
        'WEB' => __DIR__ . '/assets/templates/web/',
        'COMMON' => __DIR__ . '/assets/templates/common/',
    ];

    /**
     * Template categorization by invocation
     *
     * @var array
     */
    static $TEMPLATES = [
        'MAIN' => [
            'HEADER' => 'tpl.header.php',
            'BODY' => 'tpl.body.php',
        ],
        'COMMON' => [
            'COMMON' => 'tpl.common.php',
        ],
    ];

    /**
     * Path to 'tmp' directory to use
     *
     * @var string
     */
    static $TMP = '/tmp/';

    /**
     * Whitelisted IP's
     */
    static $WHITELIST = [
        '::1',
        '127.0.0.1',
        '10.0.0.0/8',
        '192.168.0.0/16',
    ];

    private $DEV = false;

    private $APP_ENV = false;

    /**
     * Config constructor. Perform actions upon saved states depending on deployment environment.
     */
    public function __construct()
    {
        date_default_timezone_set(self::$SYSTEM['TIMEZONE']);

        $this->APP_ENV = getenv('APP_ENV');
        if ($this->APP_ENV !== false) {
            if ($this->APP_ENV === 'DEV') {
                $this->DEV = true;
            }
        }

        if ($this->DEV === true) {
            self::$DB['HOST'] = 'db.local';
        }
    }
}
