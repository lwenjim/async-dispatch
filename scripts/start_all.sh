#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

# source "$BASE_PATH/batchStopPool.sh"

# empty=`ps -ef|grep process/pool/s# tart/queueName|grep -v grep|wc -l`
#
# while [ 0 != $empty ]
# do
#     sleep 1
# done

# "$BASE_PATH/startPool.sh" dispatch 2
# "$BASE_PATH/startPool.sh" dispatch:try 1
#
# "$BASE_PATH/startPool.sh" subTask 2
# "$BASE_PATH/startPool.sh" subTask:try 1

# Queue=('dispatch' 'subTask')

source "$BASE_PATH/init.sh"

# for (( VAR = 0; VAR < ${#Queue[@]}; ++VAR )); do
#     cmd="$BASE_PATH/startPool.sh ${Queue[VAR]} 2"
#     echo -e "exec:$cmd\n"
#     $cmd
# done

for queue in "${queues[@]}" ; do
    config=($queue);

    cmd="$BASE_PATH/start.sh ${config[0]} ${config[1]}"
    echo -e "exec:$cmd\n"
    $cmd

done
