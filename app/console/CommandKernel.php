<?php

namespace App\Console;

use App\Commands\Command;
use Framework\Logger\Logger;

class CommandKernel
{
    private $commands = [];

    public function register(Command $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    public function all(): array
    {
        return $this->commands;
    }

    public function find(string $name): ?Command
    {
        return $this->commands[$name] ?? null;
    }

    public function call(string $commandName, array $args = []): int
    {
        $command = $this->find($commandName);

        if (!$command) {
            Output::error("Command '{$commandName}' not found");
            Logger::warning("CLI: Unknown command attempted", ['command' => $commandName]);
            return 1;
        }

        $command->setArguments($this->parseArguments($args, $command->getSignature()));
        $command->setOptions($this->parseOptions($args));

        try {
            Logger::info("CLI: Running command [{$commandName}]");
            $exitCode = $command->handle();
            if ($exitCode !== 0) {
                Logger::warning("CLI: Command [{$commandName}] exited with code {$exitCode}");
            }
            return $exitCode;
        } catch (\Throwable $e) {
            Logger::exception($e, Logger::ERROR);
            throw $e;
        }
    }

    private function parseArguments(array $args, string $signature): array
    {
        preg_match_all('/\{([^}]+)\}/', $signature, $matches);
        $params = $matches[1] ?? [];

        $arguments = [];
        $argIndex  = 0;

        foreach ($params as $param) {
            if (strpos($param, '--') === 0) {
                continue;
            }

            $cleanParam = preg_replace('/[?=|].*/', '', $param);
            $cleanParam = trim($cleanParam);

            if (isset($args[$argIndex])) {
                $arguments[$cleanParam] = $args[$argIndex];
                $argIndex++;
            }
        }

        return $arguments;
    }

    private function parseOptions(array $args): array
    {
        $options = [];

        foreach ($args as $arg) {
            if (strpos($arg, '--') === 0) {
                $parts              = explode('=', substr($arg, 2));
                $options[$parts[0]] = $parts[1] ?? true;
            }
        }

        return $options;
    }
}