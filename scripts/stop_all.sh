#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

source "$BASE_PATH/init.sh"

for queue in "${queues[@]}" ; do
    config=($queue);
    cmd="source $BASE_PATH/stop.sh ${config[0]}"
    if [ -n "$1" ]; then
        cmd="$cmd $1"
    fi
    echo -e "$cmd\n"
    $cmd
done
