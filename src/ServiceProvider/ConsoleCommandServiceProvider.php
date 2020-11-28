<?php

declare(strict_types=1);

namespace Sokil\KafkaLabs\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Sokil\KafkaLabs\Command\ConsumeCommand;
use Sokil\KafkaLabs\Command\ProduceCommand;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class ConsoleCommandServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        ProduceCommand::class,
        ConsumeCommand::class,
        'consoleCommandLocator'
    ];

    public function register()
    {
        $this->getLeagueContainer()
            ->add(ConsumeCommand::class)
            ->addArgument('consumer')
            ->addArgument('kafkaConsumer');

        $this->getLeagueContainer()
            ->add(ProduceCommand::class)
            ->addArgument('producer')
            ->addArgument('kafkaProducer');

        $this->getLeagueContainer()
            ->add(
                'consoleCommandLocator',
                ContainerCommandLoader::class
            )
            ->addArgument($this->getLeagueContainer())
            ->addArgument([
                'consumer' => ConsumeCommand::class,
                'producer' => ProduceCommand::class,
            ]);

    }
}