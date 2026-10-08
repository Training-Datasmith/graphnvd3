<?php

if (!defined('_PS_VERSION_')) {
    define('_PS_VERSION_', '1.7.8.7');
}
if (!defined('_PS_ROOT_DIR_')) {
    define('_PS_ROOT_DIR_', '/srv/prestashop');
}
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', '/SRV/PrestaShop/admin-dev');
}
if (!defined('_PS_JS_DIR_')) {
    define('_PS_JS_DIR_', '/js/');
}
if (!defined('__PS_BASE_URI__')) {
    define('__PS_BASE_URI__', '/shop/');
}
if (!defined('DIRECTORY_SEPARATOR')) {
    define('DIRECTORY_SEPARATOR', '/');
}

class Module
{
    public static $installReturn = true;
    public static $registerHookReturn = true;
    public static $registerHookFailOn = null;
    public static $constructCallCount = 0;
    public static $registeredHooks = [];
    public static $transCalls = [];

    public $name;
    public $tab;
    public $version;
    public $author;
    public $need_instance;
    public $displayName;
    public $description;
    public $ps_versions_compliancy;
    public $context;

    public static function reset()
    {
        self::$installReturn = true;
        self::$registerHookReturn = true;
        self::$registerHookFailOn = null;
        self::$constructCallCount = 0;
        self::$registeredHooks = [];
        self::$transCalls = [];
    }

    public function __construct()
    {
        ++self::$constructCallCount;
    }

    public function install()
    {
        return self::$installReturn;
    }

    public function registerHook($hookName)
    {
        self::$registeredHooks[] = $hookName;
        if (self::$registerHookFailOn === $hookName) {
            return false;
        }

        return self::$registerHookReturn;
    }

    protected function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        self::$transCalls[] = [
            'id' => $id,
            'parameters' => $parameters,
            'domain' => $domain,
            'locale' => $locale,
        ];

        return $id;
    }

    public function getTranslator()
    {
        return new stdClass();
    }
}

class ModuleGraphEngine extends Module
{
    protected $_type;

    public function __construct($type)
    {
        $this->_type = $type;
    }

    public function install()
    {
        return parent::install();
    }

    public function getType()
    {
        return $this->_type;
    }
}

class Tools
{
    private static $values = [];

    public static function reset(array $values = [])
    {
        self::$values = $values;
    }

    public static function getValue($key)
    {
        return array_key_exists($key, self::$values) ? self::$values[$key] : null;
    }
}
