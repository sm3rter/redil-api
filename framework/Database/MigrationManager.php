<?php

namespace Framework\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class MigrationManager
{
    private const MIGRATIONS_TABLE = 'migrations';

    /**
     * Create the migrations table if it doesn't exist
     */
    public static function createMigrationsTable(): void
    {
        if (!Capsule::schema()->hasTable(self::MIGRATIONS_TABLE)) {
            Capsule::schema()->create(self::MIGRATIONS_TABLE, function ($table) {
                $table->increments('id');
                $table->string('migration')->unique();
                $table->integer('batch');
                $table->timestamp('executed_at')->useCurrent();
            });
        }
    }

    /**
     * Get all executed migrations
     */
    public static function getExecutedMigrations(): array
    {
        self::createMigrationsTable();
        return Capsule::table(self::MIGRATIONS_TABLE)
            ->pluck('migration')
            ->toArray();
    }

    /**
     * Get the latest batch number
     */
    public static function getLatestBatch(): int
    {
        self::createMigrationsTable();
        return (int) Capsule::table(self::MIGRATIONS_TABLE)
            ->max('batch') ?? 0;
    }

    /**
     * Record a migration as executed
     */
    public static function recordMigration(string $migration, int $batch): void
    {
        self::createMigrationsTable();
        Capsule::table(self::MIGRATIONS_TABLE)->insert([
            'migration' => $migration,
            'batch' => $batch,
        ]);
    }

    /**
     * Remove a migration record
     */
    public static function removeMigration(string $migration): void
    {
        self::createMigrationsTable();
        Capsule::table(self::MIGRATIONS_TABLE)
            ->where('migration', $migration)
            ->delete();
    }

    /**
     * Get migrations from a specific batch
     */
    public static function getMigrationsFromBatch(int $batch): array
    {
        self::createMigrationsTable();
        return Capsule::table(self::MIGRATIONS_TABLE)
            ->where('batch', $batch)
            ->orderBy('id', 'desc')
            ->pluck('migration')
            ->toArray();
    }

    /**
     * Get the batch number for a specific migration
     */
    public static function getBatchForMigration(string $migration): ?int
    {
        self::createMigrationsTable();
        return Capsule::table(self::MIGRATIONS_TABLE)
            ->where('migration', $migration)
            ->value('batch');
    }

    /**
     * Get the migration file name from full path
     */
    public static function getMigrationName(string $filepath): string
    {
        return preg_replace('/\.php$/', '', basename($filepath));
    }
}
