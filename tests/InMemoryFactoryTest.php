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

use Castor\Net\InvalidUri;
use Castor\Net\Uri;
use PHPUnit\Framework\TestCase;

/**
 * Class InMemoryFactoryTest.
 *
 * @internal
 * @covers \Castor\Queue\InMemoryFactory
 */
class InMemoryFactoryTest extends TestCase
{
    /**
     * @throws InvalidUri
     * @throws UnsupportedScheme
     */
    public function testItCreatesDriver(): void
    {
        $factory = new InMemoryFactory();
        $driver = $factory->create(Uri::parse('memory:'));
        self::assertInstanceOf(InMemoryDriver::class, $driver);
    }

    /**
     * @throws InvalidUri
     */
    public function testItThrowsErrorOnInvalidScheme(): void
    {
        $factory = new InMemoryFactory();
        $this->expectException(UnsupportedScheme::class);
        $factory->create(Uri::parse('invalid:'));
    }
}
