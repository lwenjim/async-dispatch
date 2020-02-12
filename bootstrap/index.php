<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-11
 * Time: 22:09
 */
require_once(__DIR__ . '/../vendor/autoload.php');

use AsyncDispatch\Server;
if (!in_array($func = $argv[1], ['start', 'stop']))
{
    die('deny');
}
\JimLog\Config::loadIni();
Server::getInstance($argv[2])->$func($argv[3]);
