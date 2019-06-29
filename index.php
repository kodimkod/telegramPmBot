<?php

use TelegramBot\{
    TelegramMessages,
    DatabaseConnection,
    Factory,
    DatabaseFacade,
    BotRights,
    BotTemplates
};
use TelegramPmBot\{
    BotConfig,
    BotController
};

define('APP_ROOT', __DIR__);

require APP_ROOT . '/vendor/autoload.php';

ini_set('max_execution_time', 600);
ini_set('memory_limit', -1);
set_time_limit(600);
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_error_handler(function ($errno, $errstr, $errfile, $errline ) {
    if (!error_reporting()) {
        return;
    }
    throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
});
$logDir = APP_ROOT . DIRECTORY_SEPARATOR . 'logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0777, true);
}
$messages = new TelegramMessages();
$config = new BotConfig(APP_ROOT);
$dbConnection = DatabaseConnection::fromParameters($config->getDatabaseHost(), $config->getDatabaseName(),
                $config->getDatabaseUser(), $config->getDatabasePassword());
$factory = new Factory();
$facade = new DatabaseFacade($dbConnection, $factory);

$controller = new BotController($config, $messages, $facade, $factory);
try {
    $controller->run();
} catch (\Throwable $exception) {
    file_put_contents($logDir . DIRECTORY_SEPARATOR . 'exceptions.txt', $exception->getMessage(), FILE_APPEND);
}
