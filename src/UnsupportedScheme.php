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

use Castor\Str;
use Exception;

/**
 * Class UnsupportedScheme.
 */
class UnsupportedScheme extends Exception
{
    /**
     * @param string ...$supportedSchemes
     */
    public static function create(string $passedScheme, string $factoryName, string ...$supportedSchemes): UnsupportedScheme
    {
        if ([] === $supportedSchemes) {
            $supportedSchemes[] = 'none';
        }

        return new self(sprintf(
            'Scheme "%s" is not supported by %s. Supported schemes are: %s',
            $passedScheme,
            $factoryName,
            Str\join(', ', ...$supportedSchemes)
        ));
    }
}
