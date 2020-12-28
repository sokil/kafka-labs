#!/bin/sh

docker exec -t kafkalabs_broker1 kafka-topics.sh --zookeeper kafkalabs_zookeeper:2181 --list