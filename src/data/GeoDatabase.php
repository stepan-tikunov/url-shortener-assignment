<?php

namespace assignment\data;

use assignment\Config;
use GeoIp2\Database\Reader;
use MaxMind\Db\Reader\InvalidDatabaseException;

class GeoDatabase extends Reader
{
    private static GeoDatabase | null $instance = null;

    private function __construct() {
        $config = Config::getInstance();

        try {
            parent::__construct($config->getString("geo_db_file"));
        } catch (InvalidDatabaseException $e) {
            error_log($e->getMessage());
            die();
        }
    }

    public static function getInstance(): GeoDatabase {
        if (self::$instance === null) {
            self::$instance = new GeoDatabase();
        }

        return self::$instance;
    }
}