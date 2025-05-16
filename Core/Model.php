<?php

namespace Core;

use PDO;
use App\Config;

/**
 * Base model
 *
 * PHP version 7.0
 */
abstract class Model
{
    /**
     * Get the PDO database connection
     *
     * @return PDO
     */
    protected static function getDB()
    {
        static $db = null;

        if ($db === null) {
            // Assure que Config est initialisé (au cas où ce ne serait pas fait avant)
            if (empty(Config::$DB_HOST)) {
                Config::init();
            }

            $dsn = 'mysql:host=' . Config::$DB_HOST . ';dbname=' . Config::$DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::$DB_USER, Config::$DB_PASSWORD);

            // Throw an Exception when an error occurs
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return $db;
    }
}
