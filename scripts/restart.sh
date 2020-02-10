#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

PHP_BIN_PATH=/usr/local/php/bin/php

if [[ ! -n "$1" ]] || [[ ! -n "$2" ]] ; then
    echo "usge：./restart.sh queueName procNum [signal]\n"
    exit
fi


cmd="source $BASE_PATH/stop.sh $1"

if [[ -n "$3" ]] ; then
    cmd="$cmd $3"
fi
$cmd

source "$BASE_PATH/start.sh" $1 $2
