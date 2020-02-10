#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

PHP_BIN_PATH=/usr/local/php/bin/php

if [[ ! -n "$1" ]] || [[ ! -n "$2" ]] ; then
    echo "usge：./start.sh queueName procNum\n"
    exit
fi


nohup $PHP_BIN_PATH "$BASE_PATH/../www/cli.php" "request_uri=/process/pool/start/queueName/$1/procNum/$2" 2>&1 &