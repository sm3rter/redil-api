<?php

namespace App\Commands;

class KeyGenerateCommand extends Command
{
    protected $name = 'key:generate';
    protected $description = 'Generate a new application key';
    protected $signature = 'key:generate';

    public function handle(): int
    {
        $this->info('Generating application key...');
        
        try {
            $key = base64_encode(random_bytes(32));
            $envPath = __DIR__ . '/../.env';
            $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';
            if (strpos($envContent, 'APP_KEY=') !== false) {
                $envContent = preg_replace('/^APP_KEY=.*/m', "APP_KEY={$key}", $envContent);
                echo "✓ Updated APP_KEY in .env\n";
            } else {
                if (!empty($envContent) && substr($envContent, -1) !== "\n") {
                    $envContent .= "\n";
                }
                $envContent .= "APP_KEY={$key}\n";
                echo "✓ Added APP_KEY to .env\n";
            }
            file_put_contents($envPath, $envContent);
            
            echo "$key";
        } catch (\Exception $e) {
            exit("Error generating key: " . $e->getMessage() . PHP_EOL);
        }

        return 0;
    }
}
