<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-12
 * Time: 23:49
 */


namespace AsyncDispatch\Jobs;


use AsyncDispatch\AbstractJob;
use AsyncDispatch\Jobs\Parameters\FirstParameter;

class FirstJob extends AbstractJob
{
    protected $flag = 222;
    public function getParameter():FirstParameter
    {
        return $this->parameter;
    }

    public function setParameter(FirstParameter $parameter): void
    {
        $this->parameter = $parameter;
    }

    public function handle()
    {
    }
}
