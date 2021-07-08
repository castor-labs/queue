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
        foreach ($messages as $message) {
            $callback($message);
        }
        $this->messages[$queue] = [];
    }

    /**
     * Clears the specified queue. If no queue is provided, all messages in all
     * queues are cleared.
     */
    public function clear(string $queue = null): void
    {
        if (null !== $queue) {
            $this->messages[$queue] = [];

            return;
        }
        $this->messages = [];
    }
}
