<?php

namespace assignment\data;
class UrlDatabase extends \SQLite3
{
    private static UrlDatabase | null $instance = null;
    private function __construct()
    {
        $ini = parse_ini_file("app.ini");

        if (!$ini) {
            throw new \RuntimeException("app.ini file was not loaded");
        }

        parent::__construct($ini["url_db_file"], SQLITE3_OPEN_READWRITE, null);
    }

    public static function getInstance(): UrlDatabase {
        if (UrlDatabase::$instance == null) {
            UrlDatabase::$instance = new UrlDatabase();
        }

        return UrlDatabase::$instance;
    }
}