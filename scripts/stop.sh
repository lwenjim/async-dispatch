#!/usr/bin/env bash

CURRENT_PATH=$(pwd)

BASE_PATH=$(cd `dirname $0`; pwd)

PHP_BIN_PATH=/usr/bin/php

if [ ! -n "$1" ]; then
    echo "usge：./stop.sh queueName [signal]"
    echo "signal：99-force"
    exit
fi

signal=15

if [ -n "$2" ]; then
    signal="$2"
fi
cd $BASE_PATH/../bootstrap
$PHP_BIN_PATH index.php stop $1 $2
cd $CURRENT_PATH
