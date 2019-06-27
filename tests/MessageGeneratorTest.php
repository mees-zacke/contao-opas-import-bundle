<?php

declare(strict_types=1);

namespace Floxn\ContaoOpasImportBundle\Tests;

use Floxn\ContaoOpasImportBundle\Library\MessageGenerator;
use PHPUnit\Framework\TestCase;

class MessageGeneratorTest extends TestCase
{
    public function testCanSayHelloToWorld()
    {
        $messageGenerator = new MessageGenerator();

        $message = $messageGenerator->sayHelloTo('World');

        $this->assertSame('Hello World', $message);
    }

    public function testCanNotSayHelloToEmptyTarget()
    {
        $messageGenerator = new MessageGenerator();

        $this->expectException(\InvalidArgumentException::class);

        $message = $messageGenerator->sayHelloTo('');
    }
}