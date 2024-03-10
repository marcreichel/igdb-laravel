<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use MarcReichel\IGDBLaravel\Enums\Webhook\Category;
use MarcReichel\IGDBLaravel\Exceptions\InvalidWebhookMethodException;
use Symfony\Component\Console\Command\Command;

class ConsoleTest extends TestCase
{
    public function testItShouldShowWarningForEmptyWebhooks(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks')
            ->assertExitCode(Command::FAILURE)
            ->expectsOutput('You do not have any registered webhooks.');
    }

    public function testItShouldCreateWebhook(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1337,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => false,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks:create')
            ->expectsQuestion('For which model you want to create a webhook?', 'Game')
            ->expectsQuestion('For which event do you want to create the webhook?', 'create')
            ->assertExitCode(Command::SUCCESS);
    }

    public function testItShouldFailForInvalidModelAnswerOnCreateWebhook(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Http::fake();

        $this->artisan('igdb:webhooks:create')
            ->expectsQuestion('For which model you want to create a webhook?', 1);
    }

    public function testItShouldFailForInvalidModelOnCreateWebhook(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:create')
            ->expectsQuestion('For which model you want to create a webhook?', 'FooBarBaz')
            ->assertExitCode(Command::FAILURE);
    }

    public function testItShouldFailForInvalidMethodOnCreateWebhook(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:create')
            ->expectsQuestion('For which model you want to create a webhook?', 'Game')
            ->expectsQuestion('For which event do you want to create the webhook?', 'foo')
            ->expectsOutput((new InvalidWebhookMethodException())->getMessage())
            ->assertExitCode(Command::FAILURE);
    }

    public function testItShouldSuggestModel(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => true,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks:create')
            ->expectsQuestion('For which model you want to create a webhook?', 'Games')
            ->expectsQuestion('Did you mean <comment>Game</comment>?', 'y')
            ->expectsQuestion('For which event do you want to create the webhook?', 'create')
            ->expectsOutput('Webhook created successfully!')
            ->assertExitCode(Command::SUCCESS);
    }

    public function testItShouldPrintErrorOnFailure(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:create')
            ->expectsQuestion('For which model you want to create a webhook?', 'Games')
            ->expectsQuestion('Did you mean <comment>Game</comment>?', 'y')
            ->expectsQuestion('For which event do you want to create the webhook?', 'create')
            ->expectsOutput('An error occurred while trying to create the webhook.')
            ->assertExitCode(Command::FAILURE);
    }

    public function testItShouldFailOnSuggestedModelDenial(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:create')
            ->expectsQuestion('For which model you want to create a webhook?', 'Games')
            ->expectsQuestion('Did you mean <comment>Game</comment>?', false)
            ->assertExitCode(Command::FAILURE);
    }

    public function testItShouldListWebhooks(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => true,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks')
            ->assertExitCode(Command::SUCCESS);
    }

    public function testItShouldReactivateWebhook(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1337,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => false,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks:reactivate', ['id' => 1337])
            ->assertExitCode(Command::SUCCESS)
            ->expectsOutput('Webhook reactivated.');
    }

    public function testItShouldSucceedOnActiveWebhook(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1337,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => true,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks:reactivate', ['id' => 1337])
            ->assertExitCode(Command::SUCCESS)
            ->expectsOutput('Webhook does not need to be reactivated.');
    }

    public function testItShouldFailToReactivateWebhookForUnknownWebhook(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:reactivate', ['id' => 1337])
            ->assertExitCode(Command::FAILURE)
            ->expectsOutput('Webhook not found.');
    }

    public function testItShouldFailToReactivateWebhookForInvalidModel(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1337,
                    'url' => 'https://example.com/',
                    'category' => 0,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => false,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks:reactivate', ['id' => 1337])
            ->assertExitCode(Command::FAILURE)
            ->expectsOutput('Model not found.');
    }

    public function testItShouldDeleteWebhook(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1337,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => false,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks:delete', ['id' => 1337])
            ->assertExitCode(Command::SUCCESS)
            ->expectsOutput('Webhook deleted.');
    }

    public function testItShouldFailToDeleteUnknownWebhook(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:delete', ['id' => 1337])
            ->assertExitCode(Command::FAILURE)
            ->expectsOutput('Webhook not found.');
    }

    public function testItShouldDeleteAllWebhooks(): void
    {
        Http::fake([
            '*' => Http::response([
                [
                    'id' => 1337,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => false,
                ],
                [
                    'id' => 1338,
                    'url' => 'https://example.com/',
                    'category' => Category::Game,
                    'sub_category' => 0,
                    'number_of_retries' => 0,
                    'active' => false,
                ],
            ]),
        ]);

        $this->artisan('igdb:webhooks:delete --all')
            ->assertExitCode(Command::SUCCESS)
            ->expectsOutput('All Webhooks deleted.');
    }

    public function testItShouldFailWithoutAllFlagAndMissingId(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:delete')
            ->assertExitCode(Command::FAILURE);
    }

    public function testItShouldSkipDeletionWhenNoWebhooksAreRegistered(): void
    {
        Http::fake();

        $this->artisan('igdb:webhooks:delete --all')
            ->assertExitCode(Command::SUCCESS)
            ->expectsOutput('You do not have any registered webhooks.');
    }

    public function testItShouldCallPublish(): void
    {
        $this->artisan('igdb:publish')->assertExitCode(0);
    }
}
