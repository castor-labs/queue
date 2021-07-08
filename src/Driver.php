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

/**
 * Interface Driver represents a queue driver.
 */
interface Driver
{
    /**
     * Publishes a message to a queue.
     *
     * Publishing conditions are handled by the queue implementation and
     * usually these are set in the corresponding Queue\Factory.
     *
     * @throws DriverError when the message could not be published. Implementors
     *                     MUST wrap original exceptions in this type.
     */
    public function publish(string $queue, string $message): void;

    /**
     * Consumes a message from a queue.
     *
     * Consuming SHOULD be blocking and run in a long process, however
     * implementors CAN make exceptions due to implementation reasons.
     *
     * For instance, the InMemoryDriver will return from consume once it has
     * handled all the messages that has in memory.
     *
     * @psalm-param callable(string):void $callback
     *
     * @throws DriverError when the messages cannot be retrieved for consumption
     *                     Implementors MUST wrap original exceptions in this type
     */
    public function consume(string $queue, callable $callback): void;
}
