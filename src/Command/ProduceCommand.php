<?php

declare(strict_types=1);

namespace Sokil\KafkaLabs\Command;

use RdKafka\Producer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProduceCommand extends Command
{
    /**
     * @var Producer
     */
    private $producer;

    /**
     * @param string $name
     * @param Producer $producer
     */
    public function __construct(string $name, Producer $producer)
    {
        parent::__construct($name);

        $this->producer = $producer;
    }

    protected function configure()
    {
        $this->addArgument('topicName', InputArgument::REQUIRED, 'Topic name');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $topicName = $input->getArgument('topicName');
        if (empty($topicName)) {
            throw new \RuntimeException('Topic name not specified');
        }

        $topic = $this->producer->newTopic($topicName);

        while (true) {
            $topic->produce(
                RD_KAFKA_PARTITION_UA, // stands for unassigned, and lets librdkafka choose the partition.
                0,
                \json_encode(
                    [
                        'time' => time(),
                    ]
                )
            );

            $output->writeln(
                sprintf(
                    'Message produced to topic <info>%s</info>',
                    $topicName
                )
            );

            $this->producer->flush(100);

            sleep(1);
        }
    }
}


