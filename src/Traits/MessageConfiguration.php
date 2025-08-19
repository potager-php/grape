<?php

namespace Potager\Grape\Traits;

use Potager\Grape\Contracts\MessageProviderContract;
use Potager\Grape\SimpleMessageProvider;

trait MessageConfiguration
{
    /**
     * @var MessageProviderContract|null Singleton instance of the message provider.
     */
    protected static ?MessageProviderContract $messageProvider = null;

    /**
     * Set the global message provider instance.
     *
     * @param MessageProviderContract $provider The message provider to set as the global instance.
     * @return void
     */
    public static function setMessageProvider(MessageProviderContract $provider): void
    {
        static::$messageProvider = $provider;
    }

    /**
     * Get the global message provider instance.
     *
     * If no provider has been set, a default instance of `SimpleMessageProvider` will be created and returned.
     *
     * @return MessageProviderContract The global message provider instance.
     */
    public static function getMessageProvider(): MessageProviderContract
    {
        if (static::$messageProvider === null) {
            static::$messageProvider = static::getDefaultMessageProvider();
        }

        return static::$messageProvider;
    }

    /**
     * Get the default message provider instance.
     *
     * @return MessageProviderContract The default message provider instance.
     */
    protected static function getDefaultMessageProvider(): MessageProviderContract
    {
        return new SimpleMessageProvider();
    }
}