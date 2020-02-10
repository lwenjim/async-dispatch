<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2019-07-19
 * Time: 12:21
 */

namespace AsyncDis;


trait Instance
{
    protected static $container = [];

    public static function getInstance(...$params): self
    {
        if (empty(self::$container)) {
            self::$container = new static(...$params);
        }
        return self::$container;
    }
}
