<?php

namespace App\Commands;

use Framework\Database\MigrationManager;

class MigrateCommand extends Command
{
    protected $name = 'migrate';
    protected $description = 'Run database migrations';
    protected $signature = 'migrate {--fresh : Drop all tables and re-run all migrations}';

    public function handle(): int
    {
        $this->info('Running migrations...');

        // Create migrations table if it doesn't exist
        MigrationManager::createMigrationsTable();

        $migrationsPath = __DIR__ . '/../../app/database/migrations';
        if (!is_dir($migrationsPath)) {
            $this->warning('No migrations directory found.');
            return 0;
        }

        $migrationFiles = glob($migrationsPath . '/*.php');
        if (empty($migrationFiles)) {
            $this->warning('No migrations to run.');
            return 0;
        }

        // Sort migration files by timestamp
        sort($migrationFiles);

        // Get executed migrations
        $executedMigrations = MigrationManager::getExecutedMigrations();
        $currentBatch = MigrationManager::getLatestBatch() + 1;
        $migrationsRun = 0;

        foreach ($migrationFiles as $migrationFile) {
            $migrationName = MigrationManager::getMigrationName($migrationFile);

            // Check if migration has already been executed
            if (in_array($migrationName, $executedMigrations)) {
                $this->line("Skipped: $migrationName (already executed)");
                continue;
            }

            $this->line('Running: ' . $migrationName);
            try {
                $migration = require $migrationFile;
                if (is_object($migration) && method_exists($migration, 'up')) {
                    $migration->up();
                    MigrationManager::recordMigration($migrationName, $currentBatch);
                    $this->success("  ✓ Executed: $migrationName");
                    $migrationsRun++;
                }
            } catch (\Exception $e) {
                $this->error("  ✗ Error in $migrationName: " . $e->getMessage());
                return 1;
            }
        }

        if ($migrationsRun === 0) {
            $this->info('No new migrations to run.');
        } else {
            $this->success("$migrationsRun migration(s) executed successfully!");
        }

        return 0;
    }
}
