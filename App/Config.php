<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 8+
 */
class Config
{
    /**
     * @var string
     */
    public static string $DB_HOST;
    public static string $DB_NAME;
    public static string $DB_USER;
    public static string $DB_PASSWORD;

    /**
     * @var bool
     */
    public const SHOW_ERRORS = true;

    /**
     * Initialise les valeurs dynamiques à partir des variables d'environnement
     */
    public static function init(): void
    {
        self::$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
        self::$DB_NAME = getenv('DB_NAME') ?: 'vide_grenier';
        self::$DB_USER = getenv('DB_USER') ?: 'vide_grenier_user';
        self::$DB_PASSWORD = getenv('DB_PASSWORD') ?: 'dev_password';
    }
}
