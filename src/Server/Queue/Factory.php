<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Server\Queue;

use InvalidArgumentException;

class Factory
{
    protected static function validate(string $name)
    {
        if (strlen($name) <= 6) {
            throw new InvalidArgumentException();
        }
        if ('_' !== substr($name, 5, 1)) {
            throw new InvalidArgumentException();
        }
        if (!in_array(substr($name, 0, 5), Abs::QUEUE_TYPE)) {
            throw new InvalidArgumentException();
        }

    }

    public static function factory(string $name): ?Abs
    {
        self::validate($name);
        switch (ucfirst(substr($name, 0, 5))) {
            case 'Kafka':
                return Kafka::getInstance($name);
                break;
            case 'Redis':
                return Redis::getInstance($name);
                break;
        }
        return null;
    }
}
