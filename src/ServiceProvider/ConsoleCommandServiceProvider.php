<?php

declare(strict_types=1);

namespace Sokil\KafkaLabs\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Sokil\KafkaLabs\Command\ConsumePartitionCommand;
use Sokil\KafkaLabs\Command\ProduceCommand;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

class ConsoleCommandServiceProvider extends AbstractServiceProvider
{
    private const COMMAND_NAME_CONSUME_PARTITION = 'consumePartition';
    private const COMMAND_NAME_PRODUCE = 'produce';

    protected $provides = [
        ProduceCommand::class,
        ConsumePartitionCommand::class,
        'consoleCommandLocator'
    ];

    public function register()
    {
        $this->getLeagueContainer()
            ->add(ConsumePartitionCommand::class)
            ->addArgument(self::COMMAND_NAME_CONSUME_PARTITION)
            ->addArgument(KafkaServiceProvider::SERVICE_NAME_LOW_LEVEL_CONSUMER);

        $this->getLeagueContainer()
            ->add(ProduceCommand::class)
            ->addArgument(self::COMMAND_NAME_PRODUCE)
            ->addArgument(KafkaServiceProvider::SERVICE_NAME_PRODUCER);

        $this->getLeagueContainer()
            ->add(
                'consoleCommandLocator',
                ContainerCommandLoader::class
            )
            ->addArgument($this->getLeagueContainer())
            ->addArgument([
                self::COMMAND_NAME_CONSUME_PARTITION => ConsumePartitionCommand::class,
                self::COMMAND_NAME_PRODUCE => ProduceCommand::class,
            ]);

    }
}