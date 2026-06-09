<?php

namespace App\Commands;

class MakeModelCommand extends Command
{
    protected $name = 'make:model';
    protected $description = 'Create a new Eloquent model';
    protected $signature = 'make:model {name}';

    public function handle(): int
    {
        $name = $this->argument('name');

        if (!$name) {
            $this->error('Model name is required');
            return 1;
        }

        $path = __DIR__ . '/../../app/models/' . $name . '.php';

        if (file_exists($path)) {
            $this->error("Model {$name} already exists!");
            return 1;
        }

        $tableName = strtolower($name) . 's';

        $stub = <<<'PHP'
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class {NAME} extends Model
{
    protected $table = '{TABLE}';
    protected $fillable = [];
    public $timestamps = true;
}
PHP;

        $content = str_replace(['{NAME}', '{TABLE}'], [$name, $tableName], $stub);
        file_put_contents($path, $content);

        $this->success("Model {$name} created successfully!");
        $this->line("Location: app/models/{$name}.php");
        $this->line("Table: {$tableName}");
        return 0;
    }
}
