<?php

declare(strict_types=1);

namespace Sokil\KafkaLabs\ServiceProvider;

use League\Container\ServiceProvider\AbstractServiceProvider;
use RdKafka\Conf;
use RdKafka\Consumer as KafkaLowLevelConsumer;
use RdKafka\Producer;

class KafkaServiceProvider extends AbstractServiceProvider
{
    public const SERVICE_NAME_PRODUCER_CONFIG = 'kafkaProducerConfig';
    public const SERVICE_NAME_CONSUMER_CONFIG = 'kafkaConsumerConfig';
    public const SERVICE_NAME_PRODUCER = 'kafkaProducer';
    public const SERVICE_NAME_LOW_LEVEL_CONSUMER = 'kafkaLowLevelConsumer';

    protected $provides = [
        self::SERVICE_NAME_PRODUCER_CONFIG,
        self::SERVICE_NAME_CONSUMER_CONFIG,
        self::SERVICE_NAME_PRODUCER,
        self::SERVICE_NAME_LOW_LEVEL_CONSUMER,
    ];

    public function register()
    {
        $kafkaBrokers = getenv('KAFKA_BROKERS');
        if (empty($kafkaBrokers)) {
            throw new \RuntimeException('Kafka brokers not configured');
        }

        $this->registerLowLevelConsumer($kafkaBrokers);
        $this->registerHighLevelConsumer($kafkaBrokers);
        $this->registerProducer($kafkaBrokers);
    }

    private function registerLowLevelConsumer(string $kafkaBrokers): void
    {
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
                self::SERVICE_NAME_CONSUMER_CONFIG,
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
         * Define consumer
         */
        $this->getLeagueContainer()
            ->add(
                self::SERVICE_NAME_LOW_LEVEL_CONSUMER,
                KafkaLowLevelConsumer::class
            )
            ->addArgument('kafkaConsumerConfig')
            ->addMethodCall('addBrokers', [$kafkaBrokers]);
    }

    /**
     * @link https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.examples-high-level-consumer.html
     *
     * @param string $kafkaBrokers
     */
    private function registerHighLevelConsumer(string $kafkaBrokers): void
    {

    }

    private function registerProducer(string $kafkaBrokers): void
    {
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
                self::SERVICE_NAME_PRODUCER_CONFIG,
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
         * Define producer
         */
        $this->getLeagueContainer()
            ->add(
                self::SERVICE_NAME_PRODUCER,
                Producer::class
            )
            ->addArgument('kafkaProducerConfig')
            ->addMethodCall('addBrokers', [$kafkaBrokers]);
    }
}