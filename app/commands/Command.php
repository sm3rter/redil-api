<?php

namespace App\Commands;

use App\Console\Output;

abstract class Command
{
    protected $name;
    protected $description;
    protected $signature;
    protected $arguments = [];
    protected $options = [];

    public function __construct()
    {
        $this->configure();
    }

    protected function configure(): void
    {
        // Override in subclasses
    }

    abstract public function handle(): int;

    public function getName(): string
    {
        return $this->name ?? 'unknown';
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    public function getSignature(): string
    {
        return $this->signature ?? $this->name;
    }

    protected function argument(string $name): ?string
    {
        return $this->arguments[$name] ?? null;
    }

    protected function option(string $name): ?string
    {
        return $this->options[$name] ?? null;
    }

    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    protected function call(string $command): int
    {
        global $kernel;
        return $kernel->call($command);
    }

    protected function line(string $text = ""): void
    {
        Output::line($text);
    }

    protected function info(string $text): void
    {
        Output::info($text);
    }

    protected function error(string $text): void
    {
        Output::error($text);
    }

    protected function success(string $text): void
    {
        Output::success($text);
    }

    protected function warning(string $text): void
    {
        Output::warning($text);
    }

    protected function table(array $headers, array $rows): void
    {
        Output::table($headers, $rows);
    }
}
