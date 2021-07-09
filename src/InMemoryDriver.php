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
 * Class InMemoryDriver.
 */
final class InMemoryDriver implements Driver
{
    /**
     * @psalm-var array<string,list<string>>
     */
    private array $messages;

    /**
     * InMemoryDriver constructor.
     */
    public function __construct()
    {
        $this->messages = [];
    }

    /**
     * {@inheritDoc}
     */
    public function publish(string $queue, string $message): void
    {
        $this->messages[$queue][] = $message;
    }

    /**
     * {@inheritDoc}
     */
    public function consume(string $queue, callable $callback): void
    {
        $messages = $this->messages[$queue] ?? [];
        $isCancelled = false;
        $cancel = static function () use (&$isCancelled): void {
            $isCancelled = true;
        };
        while ($message = array_shift($messages)) {
            $callback($message, $cancel);
            if (true === $isCancelled) {
                break;
            }
        }
        $this->messages[$queue] = $messages;
    }

    /**
     * Clears the specified queue. If no queue is provided, all messages in all
     * queues are cleared.
     *
     * @deprecated To be removed in 1.0.0. Use the purge method instead.
     */
    public function clear(string $queue = null): void
    {
        if (null !== $queue) {
            $this->messages[$queue] = [];

            return;
        }
        $this->messages = [];
    }

    /**
     * Purges the messages from a queue.
     */
    public function purge(string $queue): void
    {
        $this->messages[$queue] = [];
    }

    /**
     * Counts the messages in a queue.
     */
    public function count(string $queue): int
    {
        return count($this->messages[$queue] ?? []);
    }
}
