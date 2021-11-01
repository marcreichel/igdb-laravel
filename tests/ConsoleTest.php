<?php

namespace MarcReichel\IGDBLaravel\Tests;

class ConsoleTest extends TestCase
{
    public function test_list_webhooks_command(): void
    {
        $this->artisan('igdb:webhooks')
            ->expectsOutput('You do not have any registered webhooks.');
    }
}
