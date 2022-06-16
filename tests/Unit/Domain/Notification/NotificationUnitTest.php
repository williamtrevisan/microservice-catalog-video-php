<?php

namespace Domain\Notification;

use Core\Domain\Notification\Notification;
use PHPUnit\Framework\TestCase;

class NotificationUnitTest extends TestCase
{
    protected Notification $notification;

    protected function setUp(): void
    {
        $this->notification = new Notification();

        parent::setUp();
    }

    public function testShouldBeAbleToAddError()
    {
        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message'
        ]);

        $this->assertNotEmpty($this->notification->getErrors());
        $this->assertCount(1, $this->notification->getErrors());
    }

    public function testShouldBeAbleCheckIfExistsErrors()
    {
        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message'
        ]);

        $this->assertTrue($this->notification->hasErrors());
    }

    public function testShouldBeAbleToGetAnErrorsMessages()
    {
        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message'
        ]);

        $messages = $this->notification->messages();

        $this->assertEquals('video: error message', $messages);

        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message two'
        ]);

        $messages = $this->notification->messages();

        $this->assertEquals(
            'video: error message, video: error message two',
            $messages
        );
    }

    public function testShouldBeAbleToGetAnErrorsMessagesByContext()
    {
        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message'
        ]);
        $this->notification->addError([
            'context' => 'video',
                'message' => 'error message two'
        ]);
        $this->notification->addError([
            'context' => 'genre',
            'message' => 'error message three'
        ]);

        $messages = $this->notification->messages('genre');

        $this->assertEquals('genre: error message three', $messages);
    }

    public function testShouldBeAbleToGetAllErrorsMessages()
    {
        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message'
        ]);
        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message two'
        ]);
        $this->notification->addError([
            'context' => 'genre',
            'message' => 'error message three'
        ]);

        $messages = $this->notification->messages();

        $this->assertEquals(
            'video: error message, video: error message two, genre: error message three',
            $messages
        );
    }

    public function testShouldBeAbleToGetAnErrors()
    {
        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message'
        ]);
        $this->notification->addError([
            'context' => 'genre',
            'message' => 'error message two'
        ]);

        $errors = $this->notification->getErrors();

        $this->assertIsArray($errors);
        $this->assertCount(2, $errors);

        $this->notification->addError([
            'context' => 'video',
            'message' => 'error message three'
        ]);

        $errors = $this->notification->getErrors();

        $this->assertCount(3, $errors);
    }
}
