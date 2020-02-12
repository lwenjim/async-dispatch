#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

source "$BASE_PATH/init.sh"

for queue in "${queues[@]}" ; do
    config=($queue);
    # cmd=`/bin/ps -ef|grep ${config[0]}`
    # echo -e "$cmd\n"
    # $cmd

    ps -ef|grep ${config[0]}|grep -v grep
done
