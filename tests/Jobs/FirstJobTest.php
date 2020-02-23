<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-12
 * Time: 23:51
 */


namespace AsyncDispatch\Tests\Jobs;


use AsyncDispatch\App\Jobs\FirstJob;
use AsyncDispatch\App\Jobs\Parameters\FirstParameter;
use PHPUnit\Framework\TestCase;

class FirstJobTest extends TestCase
{
    /**
     * @test
     * @throws \Exception
     */
    public function handle()
    {
        $job = new FirstJob(new FirstParameter());
        $job->dispatch();
        $this->assertFalse(false);
    }
}
