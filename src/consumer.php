<?php

$conf = new \RdKafka\Conf();
//$conf->set('log_level', (string) LOG_DEBUG);
//$conf->set('debug', 'all');
$rk = new RdKafka\Consumer($conf);

$rk->addBrokers("kafka_broker1,kafka_broker2");

$topic = $rk->newTopic("test");

$topic->consumeStart(0, RD_KAFKA_OFFSET_BEGINNING);

while (true) {
    $msg = $topic->consume(0, 1000);

    if (null === $msg || $msg->err === RD_KAFKA_RESP_ERR__PARTITION_EOF) {
        // Constant check required by librdkafka 0.11.6. Newer librdkafka versions will return NULL instead.
        continue;
    } elseif ($msg->err) {
        echo $msg->errstr(), "\n";
        break;
    } else {
        echo $msg->payload, "\n";
    }
}