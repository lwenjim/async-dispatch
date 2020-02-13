<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-11
 * Time: 22:09
 */
require_once __DIR__ . '/bootstrap.php';

use AsyncDispatch\AsyncDispatch;

if (!in_array($func = $argv[1], ['start', 'stop'])) {
    die('deny');
}

AsyncDispatch::getInstance($argv[2])->$func($argv[3]);
