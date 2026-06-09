<?php

namespace App\Commands;

use Framework\Database\MigrationManager;

class RollbackCommand extends Command
{
    protected $name = 'migrate:rollback';
    protected $description = 'Rollback the last batch of migrations';
    protected $signature = 'migrate:rollback {--steps=1 : Number of batches to rollback}';

    public function handle(): int
    {
        $steps = (int) ($this->options['steps'] ?? 1);
        
        $this->info("Rolling back migrations (up to $steps batch(es))...");

        // Create migrations table if it doesn't exist
        MigrationManager::createMigrationsTable();

        $migrationsPath = __DIR__ . '/../../app/database/migrations';
        if (!is_dir($migrationsPath)) {
            $this->warning('No migrations directory found.');
            return 0;
        }

        $latestBatch = MigrationManager::getLatestBatch();
        if ($latestBatch === 0) {
            $this->info('No migrations to rollback.');
            return 0;
        }

        $rollbackFrom = max(1, $latestBatch - $steps + 1);
        $migrationsRolledBack = 0;

        for ($batch = $latestBatch; $batch >= $rollbackFrom; $batch--) {
            $batchMigrations = MigrationManager::getMigrationsFromBatch($batch);

            foreach ($batchMigrations as $migrationName) {
                $migrationFile = $migrationsPath . '/' . $migrationName . '.php';

                if (!file_exists($migrationFile)) {
                    $this->warning("Migration file not found: $migrationName");
                    MigrationManager::removeMigration($migrationName);
                    continue;
                }

                $this->line('Rolling back: ' . $migrationName);
                try {
                    $migration = require $migrationFile;
                    if (is_object($migration) && method_exists($migration, 'down')) {
                        $migration->down();
                        MigrationManager::removeMigration($migrationName);
                        $this->success("  ✓ Rolled back: $migrationName");
                        $migrationsRolledBack++;
                    }
                } catch (\Exception $e) {
                    $this->error("  ✗ Error rolling back $migrationName: " . $e->getMessage());
                    return 1;
                }
            }
        }

        if ($migrationsRolledBack === 0) {
            $this->info('No migrations to rollback.');
        } else {
            $this->success("$migrationsRolledBack migration(s) rolled back successfully!");
        }

        return 0;
    }
}
