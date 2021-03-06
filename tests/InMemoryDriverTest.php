<?php

declare(strict_types=1);

/**
 * @project Castor Queue
 * @link https://github.com/castor-labs/queue
 * @package castor/queue
 * @author Matias Navarro-Carter mnavarrocarter@gmail.com
 * @license MIT
 * @copyright 2021 CastorLabs Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Castor\Queue;

use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class InMemoryDriverTest.
 *
 * @covers \Castor\Queue\InMemoryDriver
 *
 * @internal
 */
class InMemoryDriverTest extends TestCase
{
    /**
     * @noinspection MockingMethodsCorrectnessInspection
     * @noinspection PhpParamsInspection
     * @psalm-suppress InvalidArgument
     *
     * @throws DriverError
     */
    public function testItConsumesMessages(): void
    {
        $driver = new InMemoryDriver();
        $driver->publish('messages', 'Hello World!');

        $callable = $this->getMockBuilder(stdClass::class)
            ->addMethods(['__invoke'])->getMock();
        $callable->expects(self::once())
            ->method('__invoke')
            ->with('Hello World!')
        ;

        $driver->consume('messages', $callable);
    }

    /**
     * @noinspection MockingMethodsCorrectnessInspection
     * @noinspection PhpParamsInspection
     * @psalm-suppress InvalidArgument
     *
     * @throws DriverError
     */
    public function testItConsumesMessagesOnlyOnce(): void
    {
        $driver = new InMemoryDriver();
        $driver->publish('messages', 'Hello World!');

        $callable = $this->getMockBuilder(stdClass::class)
            ->addMethods(['__invoke'])->getMock();
        $callable->expects(self::once())
            ->method('__invoke')
            ->with('Hello World!')
        ;

        $driver->consume('messages', $callable);
        $driver->consume('messages', $callable);
    }

    /**
     * @noinspection MockingMethodsCorrectnessInspection
     * @noinspection PhpParamsInspection
     * @psalm-suppress InvalidArgument
     *
     * @throws DriverError
     */
    public function testItClearsMessages(): void
    {
        $driver = new InMemoryDriver();
        $driver->publish('messages', 'Hello World!');
        $driver->clear('messages');
        $callable = $this->getMockBuilder(stdClass::class)
            ->addMethods(['__invoke'])->getMock();
        $callable->expects(self::never())
            ->method('__invoke')
        ;

        $driver->consume('messages', $callable);
    }

    /**
     * @noinspection MockingMethodsCorrectnessInspection
     * @noinspection PhpParamsInspection
     * @psalm-suppress InvalidArgument
     *
     * @throws DriverError
     */
    public function testItConsumesAllMessages(): void
    {
        $driver = new InMemoryDriver();
        $driver->publish('messages', 'Hello World!');
        $driver->publish('messages', 'Hello World 2!');
        $driver->publish('messages', 'Hello World 3!');
        $callable = $this->getMockBuilder(stdClass::class)
            ->addMethods(['__invoke'])->getMock();
        $callable->expects(self::exactly(3))
            ->method('__invoke')
        ;

        $driver->consume('messages', $callable);
        self::assertSame(0, $driver->count('messages'));
    }

    /**
     * @psalm-suppress InvalidArgument
     *
     * @throws DriverError
     */
    public function testItCancelsMessage(): void
    {
        $driver = new InMemoryDriver();
        $driver->publish('messages', 'Hello World!');
        $driver->publish('messages', 'Hello World 2!');
        $driver->publish('messages', 'Hello World 3!');

        $callable = static function (string $message, callable $cancel): void {
            self::assertSame('Hello World!', $message);
            $cancel();
        };

        $driver->consume('messages', $callable);

        self::assertSame(2, $driver->count('messages'));
    }
}
