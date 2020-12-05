<?php

declare(strict_types=1);

namespace Sokil\KafkaLabs\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use RdKafka\Conf;
use RdKafka\Consumer;
use RdKafka\Producer;

class KafkaServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'kafkaConfig',
        'kafkaProducer',
        'kafkaConsumer'
    ];

    public function register()
    {
        $kafkaBrokers = getenv('KAFKA_BROKERS');
        if (empty($kafkaBrokers)) {
            throw new \RuntimeException('Kafka brokers not configured');
        }

        /**
         * Define producer config
         *
         * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
         */
        $kafkaProducerConfig = [
            'log_level' => (string) LOG_WARNING,
            'offset.store.method' => 'broker',
            //'enable.idempotence' => true,
            //'max.in.flight.requests.per.connection' => 1,
        ];

        $kafkaProducerConfigDefinition = $this->getLeagueContainer()
            ->add(
                'kafkaProducerConfig',
                Conf::class
            );

        foreach ($kafkaProducerConfig as $kafkaConfigParam => $kafkaConfigValue) {
            $kafkaProducerConfigDefinition->addMethodCall(
                'set',
                [
                    $kafkaConfigParam,
                    $kafkaConfigValue
                ]
            );
        }

        /**
         * Define consumer config
         *
         * @link https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md
         * @link https://github.com/arnaud-lb/php-rdkafka#consumer-settings
         */
        $kafkaConsumerConfig = [
            'group.id' => 'someConsumerGroup',
            'log_level' => (string) LOG_WARNING,
            'offset.store.method' => 'broker',
        ];


        $kafkaConsumerConfigDefinition = $this->getLeagueContainer()
            ->add(
                'kafkaConsumerConfig',
                Conf::class
            );

        foreach ($kafkaConsumerConfig as $kafkaConfigParam => $kafkaConfigValue) {
            $kafkaConsumerConfigDefinition->addMethodCall(
                'set',
                [
                    $kafkaConfigParam,
                    $kafkaConfigValue
                ]
            );
        }

        /**
         * Define producer
         */
        $this->getLeagueContainer()
            ->add(
                'kafkaProducer',
                Producer::class
            )
            ->addArgument('kafkaProducerConfig')
            ->addMethodCall('addBrokers', [$kafkaBrokers]);

        /**
         * Define consumer
         */
        $this->getLeagueContainer()
            ->add(
                'kafkaLowLevelConsumer',
                Consumer::class
            )
            ->addArgument('kafkaConsumerConfig')
            ->addMethodCall('addBrokers', [$kafkaBrokers]);
    }
}