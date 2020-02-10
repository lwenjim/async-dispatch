<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-10
 * Time: 21:45
 */


namespace Tests;


use JimLog\Config;
use PHPUnit\Framework\TestCase;

class RedisTest extends TestCase
{
    use \AsyncDispatch\Redis;

    public function testRedis()
    {
        Config::loadIni();
        print_r(Config::redis()->get('pool.key.pre'));
    }
}
