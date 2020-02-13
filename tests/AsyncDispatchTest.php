<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-12
 * Time: 19:07
 */


namespace AsyncDispatch\Tests;


use AsyncDispatch\AsyncDispatch;
use PHPUnit\Framework\TestCase;

class AsyncDispatchTest extends TestCase
{
    /**
     * @test
     */
    public function start()
    {
        AsyncDispatch::getInstance()->start();
    }
}
