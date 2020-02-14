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
    protected static $container = [];

    public static function getInstance(...$params): self
    {
        $className = get_called_class();
        if (!Config::app($className)) {
            Config::app($className, new static(...$params));
        }
        return Config::app($className);
    }
}
