<?php

namespace App\Config;

class Config
{
    protected static $items = [];

    /**
     * Load all PHP config files from a directory
     *
     * @param string $dir
     */
    public static function load(string $dir)
    {
        $files = glob(rtrim($dir, '\\/') . '\\/*.php');

        foreach ($files as $file) {
            // Skip loading this Config class file if present in the config directory
            if (basename($file) === 'Config.php') {
                continue;
            }

            $key = basename($file, '.php');
            $items = require $file;

            if (is_array($items)) {
                static::$items[$key] = $items;
            }
        }
    }

    /**
     * Get a config value by key, dot notation supported
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $first = array_shift($segments);

        if (!array_key_exists($first, static::$items)) {
            return $default;
        }

        $value = static::$items[$first];

        foreach ($segments as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }

    public static function all(): array
    {
        return static::$items;
    }
}
