<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-16
 * Time: 11:13
 */


namespace AsyncDispatch\Tests\AsyncDispatch\Queues;


use AsyncDispatch\AsyncDispatch;
use AsyncDispatch\Jobs\FirstJob;
use AsyncDispatch\Jobs\Parameters\FirstParameter;
use PHPUnit\Framework\TestCase;

class Abs extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function dispatch()
    {
        $job = new FirstJob(new FirstParameter());
        $job->dispatch();
        AsyncDispatch::getInstance()->start();
    }
}
