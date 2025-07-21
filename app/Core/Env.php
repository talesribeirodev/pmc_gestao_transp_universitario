<?php
namespace App\Core;

class Env {
    public static function load($path = __DIR__ . '/../../.env') {
        if (!file_exists($path)) {
            throw new \Exception(".env file not found.");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (substr(trim($line), 0, 1) === '#') continue;

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            $_ENV[$key] = $value;
        }
    }

    public static function get(string $key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}
