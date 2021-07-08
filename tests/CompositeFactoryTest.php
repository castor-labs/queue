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
 * @internal
 * @covers \Castor\Queue\CompositeFactory
 */
class CompositeFactoryTest extends TestCase
{
    /**
     * @throws InvalidUri
     * @throws UnsupportedScheme
     */
    public function testItThrowsUnsupportedUri(): void
    {
        $factory = new CompositeFactory();
        $this->expectException(UnsupportedScheme::class);
        $factory->create(Uri::parse('some://fake.uri'));
    }

    /**
     * @throws UnsupportedScheme
     * @throws InvalidUri
     */
    public function testItCatchesUnsupportedUriFromInnerFactories(): void
    {
        $uri = Uri::parse('some://fake.uri');
        $factoryMock = $this->createMock(Factory::class);

        $factoryMock->expects(self::once())
            ->method('create')
            ->with($uri)
            ->willThrowException(new UnsupportedScheme())
        ;

        $factory = new CompositeFactory();
        $factory->add($factoryMock);
        $this->expectException(UnsupportedScheme::class);
        $this->expectExceptionMessage('Could not find any supported factories for scheme "some"');
        $factory->create($uri);
    }

    /**
     * @throws InvalidUri
     * @throws UnsupportedScheme
     */
    public function testItFindsSupportedDriver(): void
    {
        $uri = Uri::parse('some://fake.uri');
        $factoryMock = $this->createMock(Factory::class);
        $driver = $this->createStub(Driver::class);

        $factoryMock->expects(self::once())
            ->method('create')
            ->with($uri)
            ->willReturn($driver)
        ;

        $factory = new CompositeFactory();
        $factory->add($factoryMock);
        self::assertSame($driver, $factory->create($uri));
    }
}
