<?php

namespace App\Console;

class Output
{
    const RESET = "\033[0m";
    const BOLD = "\033[1m";
    const DIM = "\033[2m";
    const RED = "\033[91m";
    const GREEN = "\033[92m";
    const YELLOW = "\033[93m";
    const BLUE = "\033[94m";
    const CYAN = "\033[96m";
    const WHITE = "\033[97m";
    const GRAY = "\033[90m";

    public static function title(string $text): void
    {
        echo self::BOLD . self::CYAN . "\n ▶ " . $text . self::RESET . "\n";
    }

    public static function success(string $text): void
    {
        echo self::GREEN . "✓ " . $text . self::RESET . "\n";
    }

    public static function error(string $text): void
    {
        echo self::RED . "✗ " . $text . self::RESET . "\n";
    }

    public static function info(string $text): void
    {
        echo self::BLUE . "ℹ " . $text . self::RESET . "\n";
    }

    public static function warning(string $text): void
    {
        echo self::YELLOW . "⚠ " . $text . self::RESET . "\n";
    }

    public static function line(string $text = ""): void
    {
        echo $text . "\n";
    }

    public static function table(array $headers, array $rows): void
    {
        $columnWidths = [];
        foreach ($headers as $header) {
            $columnWidths[] = strlen($header);
        }

        foreach ($rows as $row) {
            foreach ($row as $idx => $cell) {
                $columnWidths[$idx] = max($columnWidths[$idx] ?? 0, strlen((string) $cell));
            }
        }

        echo "\n";
        foreach ($headers as $idx => $header) {
            echo self::BOLD . str_pad($header, $columnWidths[$idx] + 2) . self::RESET;
        }
        echo "\n";

        foreach ($headers as $idx => $width) {
            echo str_repeat("─", $columnWidths[$idx] + 2);
        }
        echo "\n";

        foreach ($rows as $row) {
            foreach ($row as $idx => $cell) {
                echo str_pad((string) $cell, $columnWidths[$idx] + 2);
            }
            echo "\n";
        }
        echo "\n";
    }
}
