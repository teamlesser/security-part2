<?php
/*******************************************************************************
 * File: config.class.php
 *
 * Desc: Class Config for Projekt. Reads the data from config.php
 * and serves it through a single instance.
 *
 * Date: 2018-05-08
 ******************************************************************************/

/**
 * Class Config
 * A singleton class that serves the
 * data from config.php thorough the
 * server.
 */
class Config {
    /**
     * @var Config The single instance of this class.
     */
    protected static $instance;

    /**
     * Contains settings read from config.php
     * @var array Associative array with key-value pairs
     */
    private static $settings;

    /**
     * Config constructor.
     * Reads settings from config.php.
     * Private due to class being singleton.
     */
    private function __construct(){
        // config.php returns an array
        self::$settings = include(dirname(__DIR__) . "/config.php");
    }

    /**
     * Returns the current instance if one exists,
     * otherwise creates it.
     * @return Config The single instance of this class.
     */
    public static function getInstance(){
        if (!self::$instance){
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Gets the value for a setting.
     * @param $setting string The name of the setting.
     * @return bool|mixed Either the value for the key or false (setting is not set).
     */
    public function getSetting($setting){
        if (isset(self::$settings[$setting])) {
            return self::$settings[$setting];
        }

        return false;
    }
}