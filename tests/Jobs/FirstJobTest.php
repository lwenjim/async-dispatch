<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-12
 * Time: 23:51
 */


namespace AsyncDispatch\Tests\Jobs;


use AsyncDispatch\Jobs\FirstJob;
use AsyncDispatch\Jobs\Parameters\FirstParameter;
use PHPUnit\Framework\TestCase;

class FirstJobTest extends TestCase
{
    /**
     * @test
     */
    public function handle()
    {
        $job = new FirstJob(new FirstParameter());
        $job->dispatch();
    }
}
