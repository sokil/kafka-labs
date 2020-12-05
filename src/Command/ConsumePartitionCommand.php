<?php

declare(strict_types=1);

namespace Sokil\KafkaLabs\Command;

use RdKafka\Consumer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConsumePartitionCommand extends Command
{
    private Consumer $consumer;

    /**
     * @param string $name
     * @param Consumer $consumer
     */
    public function __construct(string $name, Consumer $consumer)
    {
        parent::__construct($name);

        $this->consumer = $consumer;
    }

    protected function configure()
    {
        $this
            ->addArgument(
                'topicName',
                InputArgument::REQUIRED,
                'Topic name'
            )
            ->addOption(
                'partition',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Partition id',
                0
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $topicName = $input->getArgument('topicName');
        if (empty($topicName)) {
            throw new \RuntimeException('Topic name not specified');
        }

        $partition = $input->getOption('partition');
        if (!is_numeric($partition)) {
            throw new \RuntimeException('Topic name not specified');
        }

        $partition = (int)$partition;

        $topic = $this->consumer->newTopic($topicName);

        /**
         * Allowed: RD_KAFKA_OFFSET_BEGINNING, RD_KAFKA_OFFSET_END, RD_KAFKA_OFFSET_STORED.
         */
        $consumptionStartPosition = RD_KAFKA_OFFSET_BEGINNING;

        $topic->consumeStart($partition, $consumptionStartPosition);

        while (true) {
            $msg = $topic->consume($partition, 1000);

            $output->write(sprintf(
                'Message consumed from topic <info>%s</info> and partition <info>%s</info>: ',
                $topicName,
                $partition
            ));

            if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
                // Constant check required by librdkafka 0.11.6. Newer librdkafka versions will return NULL instead.
                $output->writeln('No message');
                sleep(1);
                continue;
            } elseif ($msg->err) {
                $output->writeln($msg->errstr());
                break;
            } else {
                $output->writeln($msg->payload);
            }
        }
    }
}

