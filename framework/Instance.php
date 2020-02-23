<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2019-07-19
 * Time: 12:21
 */

namespace AsyncDispatch;


trait Instance
{
    protected static $container = false;

    public static function getInstance(...$params): self
    {
        if (static::$container === false) {
            static::$container = new static(...$params);
        }
        return static::$container;
    }
}
