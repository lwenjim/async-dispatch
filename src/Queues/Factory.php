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
    public static function factory(): ?Abs
    {
        switch (ucfirst((string)Config::get('queue.dirver'))) {
            case 'Kafka':
                return Kafka::getInstance();
                break;
            case 'Redis':
                return Redis::getInstance();
                break;
        }
        return null;
    }
}
