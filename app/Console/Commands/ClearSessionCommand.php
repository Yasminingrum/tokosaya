<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearSessionCommand extends Command
{
    protected $signature = 'session:clear';
    protected $description = 'Clear all session files';

    public function handle()
    {
        $path = storage_path('framework/sessions');
        File::cleanDirectory($path);
        $this->info('Session files cleared!');
    }
}
