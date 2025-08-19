<?php

namespace Potager\Grape\Traits;

use Potager\Grape\Contracts\ErrorCollectorContract;
use Potager\Grape\SimpleErrorCollector;


trait CollectorConfiguration
{
    /**
     * @var callable|null
     */
    protected static $collectorFactory = null;

    /**
     * Set the factory for creating error collectors.
     *
     * @param callable $collectorFactory A callable that returns an instance of ErrorCollectorContract.
     * @return void
     * @throws \InvalidArgumentException If the provided factory does not return an instance of ErrorCollectorContract.
     */
    public static function setErrorCollector(callable $collectorFactory): void
    {
        // Test the factory to ensure it returns a valid ErrorCollectorContract instance
        $testInstance = $collectorFactory();
        if (!$testInstance instanceof ErrorCollectorContract) {
            throw new \InvalidArgumentException('The provided factory must return an instance of ErrorCollectorContract.');
        }

        static::$collectorFactory = $collectorFactory;
    }

    /**
     * Get the error collector instance using the factory.
     *
     * @return ErrorCollectorContract
     */
    public static function getErrorCollector(): ErrorCollectorContract
    {
        $factory = static::$collectorFactory ?? static::getDefaultCollectorFactory();
        return $factory();
    }

    /**
     * Get the default factory for creating error collectors.
     *
     * @return callable
     */
    protected static function getDefaultCollectorFactory(): callable
    {
        return fn(): SimpleErrorCollector => new SimpleErrorCollector();
    }
}