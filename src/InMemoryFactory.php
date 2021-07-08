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
 * Class InMemoryFactory.
 */
final class InMemoryFactory implements Factory
{
    private const SCHEME = 'memory';

    /**
     * @throws UnsupportedScheme
     */
    public function create(Uri $uri): Driver
    {
        $scheme = $uri->getScheme();
        if (self::SCHEME !== $scheme) {
            throw UnsupportedScheme::create($scheme, __CLASS__, self::SCHEME);
        }

        return new InMemoryDriver();
    }
}
