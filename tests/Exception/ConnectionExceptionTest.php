<?php

declare(strict_types=1);

namespace Manuxi\GoogleReviewsBundle\Tests\Exception;

use Exception;
use Manuxi\GoogleReviewsBundle\Exception\ConnectionException;
use PHPUnit\Framework\TestCase;

class ConnectionExceptionTest extends TestCase
{
    public function testExtendsException(): void
    {
        $exception = new ConnectionException();

        $this->assertInstanceOf(Exception::class, $exception);
    }

    public function testWithMessage(): void
    {
        $message = 'Connection failed';
        $exception = new ConnectionException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testWithCode(): void
    {
        $exception = new ConnectionException('Error', 500);

        $this->assertSame(500, $exception->getCode());
    }

    public function testWithPreviousException(): void
    {
        $previous = new Exception('Previous error');
        $exception = new ConnectionException('Current error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testIsThrowable(): void
    {
        $this->expectException(ConnectionException::class);
        $this->expectExceptionMessage('Test exception');

        throw new ConnectionException('Test exception');
    }
}
