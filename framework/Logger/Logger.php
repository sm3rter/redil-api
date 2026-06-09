<?php

namespace Framework\Logger;

class Logger
{
    const DEBUG    = 'DEBUG';
    const INFO     = 'INFO';
    const WARNING  = 'WARNING';
    const ERROR    = 'ERROR';
    const CRITICAL = 'CRITICAL';

    private static string $logPath = '';

    public static function getLogPath(): string
    {
        if (self::$logPath !== '') {
            return self::$logPath;
        }

        $root = dirname(__DIR__, 2);
        $dir  = $root . '/framework/logs';

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        self::$logPath = $dir . '/redil.log';
        return self::$logPath;
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        $timestamp  = date('Y-m-d H:i:s');
        $env        = $_ENV['APP_ENV'] ?? 'unknown';
        $contextStr = empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $line = "[{$timestamp}] {$env}.{$level}: {$message}{$contextStr}" . PHP_EOL;

        $path = self::getLogPath();

        if (file_exists($path) && filesize($path) > 10 * 1024 * 1024) {
            @rename($path, $path . '.' . date('Y-m-d-His'));
        }

        @file_put_contents($path, $line, FILE_APPEND | LOCK_EX);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log(self::DEBUG, $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log(self::INFO, $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log(self::WARNING, $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log(self::ERROR, $message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log(self::CRITICAL, $message, $context);
    }

    public static function exception(\Throwable $e, string $level = self::ERROR): void
    {
        self::log($level, $e->getMessage(), [
            'exception' => get_class($e),
            'file'      => $e->getFile(),
            'line'      => $e->getLine(),
            'trace'     => self::formatTrace($e->getTraceAsString()),
        ]);
    }

    private static function formatTrace(string $trace): string
    {
        $lines = explode("\n", $trace);
        $lines = array_slice($lines, 0, 15);
        return implode(' | ', array_map('trim', $lines));
    }
}