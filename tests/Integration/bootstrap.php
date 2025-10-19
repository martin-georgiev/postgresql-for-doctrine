<?php

declare(strict_types=1);
use Doctrine\DBAL\DriverManager;

require_once __DIR__.'/../../vendor/autoload.php';

// Create test schema if it doesn't exist
// Get environment variables with proper type casting
/** @phpstan-ignore-next-line */
$host = isset($_ENV['POSTGRES_HOST']) ? (string) $_ENV['POSTGRES_HOST'] : 'localhost';
/** @phpstan-ignore-next-line */
$port = isset($_ENV['POSTGRES_PORT']) ? (int) $_ENV['POSTGRES_PORT'] : 5432;
/** @phpstan-ignore-next-line */
$dbname = isset($_ENV['POSTGRES_DB']) ? (string) $_ENV['POSTGRES_DB'] : 'postgres_doctrine_test';
/** @phpstan-ignore-next-line */
$user = isset($_ENV['POSTGRES_USER']) ? (string) $_ENV['POSTGRES_USER'] : 'postgres';
/** @phpstan-ignore-next-line */
$password = isset($_ENV['POSTGRES_PASSWORD']) ? (string) $_ENV['POSTGRES_PASSWORD'] : 'postgres';

$connectionParams = [
    'driver' => 'pdo_pgsql',
    'host' => $host,
    'port' => $port,
    'dbname' => $dbname,
    'user' => $user,
    'password' => $password,
];

try {
    $connection = DriverManager::getConnection($connectionParams);
    $connection->executeStatement('CREATE SCHEMA IF NOT EXISTS test');
    $connection->close();
} catch (Exception $exception) {
    \error_log('Warning: Could not create test schema: '.$exception->getMessage());
}
