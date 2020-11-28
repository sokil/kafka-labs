<?php

require_once __DIR__ . './../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Console\Application;
use League\Container\Container;
use Sokil\KafkaLabs\ServiceProvider\KafkaServiceProvider;
use Sokil\KafkaLabs\ServiceProvider\ConsoleCommandServiceProvider;

$container = new Container();
$container->addServiceProvider(ConsoleCommandServiceProvider::class);
$container->addServiceProvider(KafkaServiceProvider::class);

$dotenv = new Dotenv();
$dotenv->usePutenv(true);
$dotenv->load(__DIR__ . '/../.env');

$app = new Application();
$app->setCommandLoader($container->get('consoleCommandLocator'));

$app->run();