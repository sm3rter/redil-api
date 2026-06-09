<?php

namespace App\Commands;

use App\Console\Output;

class HelpCommand extends Command
{
    protected $name = 'help';
    protected $description = 'Show help for commands';
    protected $signature = 'help {command?}';

    public function handle(): int
    {
        global $kernel;

        $commandName = $this->argument('command');

        if ($commandName) {
            $command = $kernel->find($commandName);
            if (!$command) {
                $this->error("Command '{$commandName}' not found");
                return 1;
            }

            Output::title("Command: {$commandName}");
            $this->line($command->getDescription());
            $this->line("\nSignature: {$command->getSignature()}");
            return 0;
        }

        // Show all commands
        Output::title('Available Commands');

        $commands = $kernel->all();
        $rows = [];

        foreach ($commands as $cmd) {
            $rows[] = [$cmd->getName(), $cmd->getDescription()];
        }

        $this->table(['Command', 'Description'], $rows);
        return 0;
    }
}
