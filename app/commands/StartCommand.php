<?php

namespace App\Commands;

use App\Console\Output;

class StartCommand extends Command
{
    protected $name = 'start';
    protected $description = 'Start the development server';
    protected $signature = 'start {host=127.0.0.1} {port=8000}';

    public function handle(): int
    {
        $host = $this->argument('host') ?? '127.0.0.1';
        $port = $this->argument('port') ?? '8000';

        Output::title('Development Server');
        $this->info("Starting server at http://{$host}:{$port}");
        $this->line("Press Ctrl+C to stop\n");

        exec("php -S $host:$port -t public");
        return 0;
    }
}
