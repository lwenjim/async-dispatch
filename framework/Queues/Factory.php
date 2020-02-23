<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Queues;

use AsyncDispatch\Config;

class Factory
{
    public static function makeQueue(): ?Abs
    {
        $driver = sprintf('\AsyncDispatch\Queues\\%s', ucfirst((string)Config::get('queue.dirver')));
        return $driver::getInstance();
    }
}
