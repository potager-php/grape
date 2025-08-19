<?php

namespace Potager\Grape\Traits;

use PDO;

/**
 * Trait DatabaseConfiguration
 * 
 * Provides database connection management for validators that require database access.
 * 
 * This trait enables validators to perform database-related validations such as
 * uniqueness checks, foreign key validations, and other database-dependent rules.
 * Supports any PDO-compatible database (MySQL, PostgreSQL, SQLite, SQL Server, etc.).
 */
trait DatabaseConfiguration
{
    /**
     * The PDO database connection instance used for database validations.
     */
    protected static ?PDO $pdoInstance = null;

    /**
     * Create a new database connection using DSN, credentials, and options.
     * 
     * @param string $dsn Data Source Name (e.g., 'mysql:host=localhost;dbname=app')
     * @param string|null $user Database username
     * @param string|null $password Database password
     * @param array $options PDO connection options
     * 
     * @example Grape::connect('mysql:host=localhost;dbname=app', 'user', 'password')
     * @example Grape::connect('sqlite:/path/to/database.db')
     * @example Grape::connect('pgsql:host=localhost;dbname=app', 'user', 'pass', [PDO::ATTR_PERSISTENT => true])
     */
    public static function connect(string $dsn, ?string $user = null, ?string $password = null, array $options = []): void
    {
        self::$pdoInstance = new PDO($dsn, $user, $password, $options);
    }

    /**
     * Set an existing PDO instance for database validations.
     * 
     * Use this method when you want to reuse an existing database connection
     * or when using dependency injection containers.
     * 
     * @param PDO $pdo The PDO instance to use for database validations
     * 
     * @example Grape::setPDO($container->get('database.connection'))
     */
    public static function setPDO(PDO $pdo): void
    {
        self::$pdoInstance = $pdo;
    }

    /**
     * Get the current PDO database connection instance.
     * 
     * @return PDO|null The PDO instance, or null if no connection is configured
     */
    public static function getPDO(): ?PDO
    {
        return self::$pdoInstance;
    }
}