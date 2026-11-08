<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;
class MakeControllerCommand extends Command
{
    protected $name = 'make:controller';
    protected $description = 'Create a new controller';
    protected $signature = 'make:controller {name}';

    public function handle(): int
    {
        $name = $this->argument('name');

        if (!$name) {
            $this->error('Controller name is required');
            return 1;
        }

        $path = base_path('app/controllers') . '/' . $name . '.php';

        if (file_exists($path)) {
            $this->error("Controller {$name} already exists!");
            return 1;
        }

        $stub = <<<'PHP'
<?php

namespace App\Controllers;

use Framework\Http\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class {NAME} extends Controller
{
    public function index(Request $request, Response $response): Response
    {
        return $this->response($response, ['message' => 'Hello from {NAME}']);
    }
}
PHP;

        $content = str_replace('{NAME}', $name, $stub);
        file_put_contents($path, $content);

        $this->success("Controller {$name} created successfully!");
        $this->line("Location: app/controllers/{$name}.php");
        return 0;
    }
}
