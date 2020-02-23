<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-16
 * Time: 11:13
 */


namespace AsyncDispatch\Tests\AsyncDispatch\Queues;


use AsyncDispatch\App\Jobs\FirstJob;
use AsyncDispatch\App\Jobs\Parameters\FirstParameter;
use AsyncDispatch\AsyncDispatch;
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
