<?php

namespace App\Commands;

use Framework\Database\MigrationManager;

class MigrateStatusCommand extends Command
{
    protected $name = 'migrate:status';
    protected $description = 'Show the status of each migration';
    protected $signature = 'migrate:status';

    public function handle(): int
    {
        $this->info('Migration Status');
        $this->line(str_repeat('=', 60));

        // Create migrations table if it doesn't exist
        MigrationManager::createMigrationsTable();

        $migrationsPath = __DIR__ . '/../../app/database/migrations';
        if (!is_dir($migrationsPath)) {
            $this->warning('No migrations directory found.');
            return 0;
        }

        $migrationFiles = glob($migrationsPath . '/*.php');
        if (empty($migrationFiles)) {
            $this->warning('No migrations found.');
            return 0;
        }

        // Sort migration files
        sort($migrationFiles);

        $executedMigrations = MigrationManager::getExecutedMigrations();

        $headers = ['Migration', 'Status', 'Batch'];
        $rows = [];

        foreach ($migrationFiles as $migrationFile) {
            $migrationName = MigrationManager::getMigrationName($migrationFile);
            $isExecuted = in_array($migrationName, $executedMigrations);
            
            $status = $isExecuted ? '✓ Ran' : '○ Pending';
            $batch = $isExecuted ? MigrationManager::getBatchForMigration($migrationName) : '-';

            $rows[] = [
                $migrationName,
                $status,
                $batch
            ];
        }

        $this->table($headers, $rows);
        $this->line(str_repeat('=', 60));

        return 0;
    }
}
