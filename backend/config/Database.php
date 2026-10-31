
<?php

class Database
{
    private static ?PDO $connection = null;

    public static function connect(): PDO
    {
        if (self::$connection === null) {
            $configPath = __DIR__ . '/config.php';

            if (!file_exists($configPath)) {
                throw new RuntimeException(
                    'Database configuration not found. Copy config.example.php to config.php.'
                );
            }

            $config = require $configPath;
            $dbConfig = $config['database'];

            $host = $dbConfig['host'];
            $port = $dbConfig['port'];
            $database = $dbConfig['name'];
            $username = $dbConfig['username'];
            $password = $dbConfig['password'];
            $charset = $dbConfig['charset'];

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

            self::$connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return self::$connection;
    }
}
