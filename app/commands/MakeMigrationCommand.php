<?php

namespace App\Commands;

class MakeMigrationCommand extends Command
{
    protected $name = 'make:migration';
    protected $description = 'Create a new database migration';
    protected $signature = 'make:migration {name}';

    public function handle(): int
    {
        $name = $this->argument('name');

        if (!$name) {
            $this->error('Migration name is required');
            return 1;
        }

        // Extract table name from migration name
        // Handles: create_users_table, add_email_to_users_table, drop_users_table
        $tableName = 'table_name'; // default fallback

        if (preg_match('/_([\w]+)_table$/', $name, $matches)) {
            $tableName = $matches[1];
        }

        $migrationsDir = __DIR__ . '/../../app/database/migrations';
        if (!is_dir($migrationsDir)) {
            mkdir($migrationsDir, 0755, true);
        }

        $filename = date('Y_m_d_His') . '_' . $name . '.php';
        $path = $migrationsDir . '/' . $filename;

        $stub = <<<PHP
<?php

use Illuminate\Database\Capsule\Manager as Capsule;

return new class {
    public function up()
    {
        if (!Capsule::schema()->hasTable('{$tableName}')) {
            Capsule::schema()->create('{$tableName}', function (\$table) {
                \$table->id();
                \$table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Capsule::schema()->hasTable('{$tableName}')) {
            Capsule::schema()->drop('{$tableName}');
        }
    }
};
PHP;

        file_put_contents($path, $stub);

        $this->success("Migration {$filename} created successfully!");
        $this->line("Location: app/database/migrations/{$filename}");
        return 0;
    }
}