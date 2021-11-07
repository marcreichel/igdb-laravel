<?php

namespace MarcReichel\IGDBLaravel\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    protected $signature = 'igdb:publish';

    protected $description = 'Publish IGDB-Laravel configuration.';

    public function handle(): void
    {
        $this->call('vendor:publish', ['--tag' => 'igdb:config', '--force' => true]);
    }
}
