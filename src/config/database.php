<?php

function loadEnv($path = null)
{
    if ($path === null) {
        $path = __DIR__ . '/../.env';
    }

    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (getenv($name) === false) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

loadEnv();

define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_NAME', getenv('DB_NAME') ?: 'jm_informatica');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'root');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_CHARSET', 'utf8mb4');

define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') === 'true');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8080');

define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', intval(getenv('SMTP_PORT') ?: 587));
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'JM Informática');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: '');
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');

function getConnection()
{
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci",
        ];
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        if (APP_DEBUG) {
            die("Erro de conexão: " . $e->getMessage());
        } else {
            die("Erro de conexão com o banco de dados.");
        }
    }
}
