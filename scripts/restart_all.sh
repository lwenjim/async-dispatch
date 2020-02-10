#!/usr/bin/env bash

BASE_PATH=$(cd `dirname $0`; pwd)

PHP_BIN_PATH=/usr/local/php/bin/php

source "$BASE_PATH/stop_all.sh" 9

source "$BASE_PATH/start_all.sh"