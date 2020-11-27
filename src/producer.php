<?php

$conf = new RdKafka\Conf();
$conf->set('log_level', (string) LOG_DEBUG);
$conf->set('debug', 'all');

$rk = new RdKafka\Producer($conf);

$rk->addBrokers("kafka_broker1,kafka_broker2");

$topic = $rk->newTopic("test");

$topic->produce(RD_KAFKA_PARTITION_UA, 0, "Message payload");

$rk->flush(100);