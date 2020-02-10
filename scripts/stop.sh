#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

PHP_BIN_PATH=/usr/local/php/bin/php

if [ ! -n "$1" ]; then
    echo "usge：./stop.sh queueName [signal]"
    echo "signal：99-force"
    exit
fi

signal=15

if [ -n "$2" ]; then
    signal="$2"
fi

cmd="$PHP_BIN_PATH $BASE_PATH/../www/cli.php request_uri=/process/pool/stop/queueName/$1/signal/$signal"

echo $cmd

$cmd
