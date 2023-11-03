<?php

namespace assignment;
class Config {
    private array $ini;

    private static Config | null $instance = null;

    private function __construct() {
        $this->ini = parse_ini_file("app.ini");
    }

    public static function getInstance(): Config {
        if (self::$instance == null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    public function getString(string $key): string {
        return $this->ini[$key];
    }

    public function getInteger(string $key): int {
        return intval($this->ini[$key]);
    }
}