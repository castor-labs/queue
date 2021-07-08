# Introduction

Castor Queue provides abstractions over queue drivers. You can code to these abstractions in your own projects
and then flexibly change the implementations for one that suits your project better.

## Basic Usage

Every `Castor\Queue\Driver` implementation has a corresponding `Castor\Queue\Factory` one. 
The factories use a `Castor\Net\Uri` object to create the driver with the proper configuration. URIs are extremely
flexible and can accommodate complex drivers by allowing to pass options in the form of query parameters.

Factories are intended to be registered in your application using a Dependency Injection framework. For instance, you
can bind `Castor\Queue\CompositeFactory` to the `Castor\Queue\Factory` class and register all the sub factories your
application supports. You can easily swap the active implementation by changing the URI provided to the create method.
The composite factory will find te first driver supported by the passed uri scheme.

## Design Principles

The abstractions are intentionally simplified to support most common and basic operations. For example, the main
interface `Castor\Queue\Driver` defines only two methods: `publish` and `consume`.

If you need capabilities specific to your queue implementation, it is most likely that the implementation provides
those operations in their public api, but you will have to check for that implementation type using the `instanceof`
operator, so you can be sure you are working with that driver. Then, as part of the logic of your application you
could fallback to other kinds of behaviours.

## Available Implementations

This project contains one implementation that stores messages in memory. This implementation is very useful for testing
purposes, but obviously unsuitable for production environments. If you need more reliable implementations, you can
find them in one of the `castor/*-pack` packages. For instance, `castor/aws-pack` contains the SQS driver, among
other services implementing other Castor interfaces. We personally recommend `castor/amqp-pack`.

## Best Practices for Implementors

If you are using this package to create your own queue implementation, we encourage you to follow these practices.

### 1. Keep the implementation of the interface simple

Stick to what the `publish` and `consume` methods promise to do and nothing else. You can create an extended public
api in your implementation so client code might potentially use it.

For instance, the `Castor\Queue\InMemoryDriver` provides methods to clear a particular queue of messages. While this is
a useful operation, it is not the central part of a queue driver, which is to publish and consume messages. It the users
of your implementation wish to clear the queue, they will have to 

Following the Interface Segregation principle, other interfaces might be included in the future if the functionality
is sufficiently common.

### 2. Use good OOP to extend functionality

It is not a responsibility of the driver implementation to do anything else than consuming a queue in a blocking way,
because the `consume` method is thought of to be executed in worker contexts. This means that if you want to add
functionality like limit the messages to consume to a certain number and then terminate the process, or terminate the
process when a certain memory limit is reached, you must handle that in client code. Decorators and composition are always
the best answer in these cases. For instance, this naive implementation cuts the queue process when 100 messages have
been consumed:

```php
<?php

use Castor\Queue\Driver;

class ConsumeLimitDriver implements Driver
{
    private Driver $driver;
    private int $limit;
    
    public function __construct(Driver $driver, int $limit = 100)
    {
        $this->driver = $driver;
        $this->limit = $limit;
    }
    
    public function publish(string $queue, string $message) : void
    {
        $this->driver->publish($queue, $message);
    }

    public function consume(string $queue, callable $callback) : void
    {
        $count = 0;
        $limiter = function (string $message) use ($callback, $count) {
            $callback($message); // Process the message.
            $count++;
            if ($this->limit <= $count) {
                exit(1);
            }
        };
        $this->driver->consume($queue, $limiter);
    }
}
```

You can a similar implementation to cut execution on memory limits.

### 3. Always provide defaults

If your implementation needs a password or a host or a port, always default to dummy values when reading data from the URI.
For example, in the AMQP implementation, the host is always localhost if an empty host is provided. Similarly, the port
is the one traditionally used by RabbitMQ and the username and password is guest. It is up to client code to provide the
correct values.