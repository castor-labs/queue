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

use Castor\Net\Uri;

/**
 * Class CompositeFactory.
 */
final class CompositeFactory implements Factory
{
    /**
     * @var Factory[]
     */
    private array $factories;

    /**
     * CompositeFactory constructor.
     *
     * @param Factory ...$factories
     */
    public function __construct(Factory ...$factories)
    {
        $this->factories = $factories;
    }

    public function add(Factory $factory): void
    {
        $this->factories[] = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function create(Uri $uri): Driver
    {
        foreach ($this->factories as $factory) {
            try {
                return $factory->create($uri);
            } catch (UnsupportedScheme $e) {
                continue;
            }
        }

        throw new UnsupportedScheme(sprintf(
            'Could not find any supported factories for scheme "%s"',
            $uri->getScheme()
        ));
    }
}
