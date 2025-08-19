<?php

namespace Potager\Grape\Traits;

use Potager\Grape\Helpers\HelpersFacade;

/**
 * Trait HelperManagement
 * 
 * Provides access to validation helper utilities and functions.
 * 
 * This trait manages the HelpersFacade instance which contains various
 * utility functions for validation tasks such as URL validation, credit card
 * validation, phone number validation, and other common validation helpers.
 */
trait HelperManagement
{
    /**
     * The singleton instance of the helpers facade.
     */
    protected static ?HelpersFacade $helpers = null;

    /**
     * Get the helpers facade instance.
     * 
     * Returns a singleton instance of the HelpersFacade containing various
     * validation utility functions. Creates a new instance if none exists.
     * 
     * @return HelpersFacade The helpers facade with validation utilities
     * 
     * @example Grape::helpers()->isValidUrl('https://example.com')
     * @example Grape::helpers()->isValidCreditCard('4111111111111111')
     */
    public static function helpers(): HelpersFacade
    {
        if (static::$helpers === null) {
            static::$helpers = new HelpersFacade();
        }
        return static::$helpers;
    }

}
