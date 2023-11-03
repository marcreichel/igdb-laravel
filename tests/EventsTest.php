<?php

declare(strict_types=1);

namespace MarcReichel\IGDBLaravel\Tests;

/**
 * @internal
 */
class EventsTest extends TestCase
{
    /**
     * @dataProvider modelsDataProvider
     */
    public function testItShouldHaveCreatedEventForEveryModel(string $className): void
    {
        $eventClassString = 'MarcReichel\IGDBLaravel\Events\\' . $className . 'Created';
        $this->assertTrue(class_exists($eventClassString));
    }

    /**
     * @dataProvider modelsDataProvider
     */
    public function testItShouldHaveUpdatedEventForEveryModel(string $className): void
    {
        $eventClassString = 'MarcReichel\IGDBLaravel\Events\\' . $className . 'Updated';
        $this->assertTrue(class_exists($eventClassString));
    }

    /**
     * @dataProvider modelsDataProvider
     */
    public function testItShouldHaveDeletedEventForEveryModel(string $className): void
    {
        $eventClassString = 'MarcReichel\IGDBLaravel\Events\\' . $className . 'Deleted';
        $this->assertTrue(class_exists($eventClassString));
    }
}
