#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

LIST_SCRIPT_PATH="$BASE_PATH/list.sh"

RESTART_SCRIPT_PATH="$BASE_PATH/restart_all.sh"

LOG_PATH="$BASE_PATH/../runtime/log/run.log"

count=`"$LIST_SCRIPT_PATH"|wc -l`

if [[ "0" == "$count" ]];then
    nohup bash "$RESTART_SCRIPT_PATH" >>"$LOG_PATH" 2>&1 &
fi
