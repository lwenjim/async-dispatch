#!/usr/bin/env bash

CURRENT_PATH=$(pwd)

BASE_PATH=$(cd `dirname $0`; pwd)

PHP_BIN_PATH=/usr/bin/php

if [[ ! -n "$1" ]] || [[ ! -n "$2" ]] ; then
    echo "usge：./start.sh queueName procNum\n"
    exit
fi

cd $BASE_PATH/../bootstrap
cmd="$PHP_BIN_PATH index.php start $1 $2 AsyncDispatch"
echo -e $cmd
nohup  $cmd  2>&1 &
cd $CURRENT_PATH
