<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch;


class AsyncDispatch
{
    use Instance;
    protected $queue = null;
    protected $pool  = null;

    public function getPool(): Pool
    {
        return $this->pool;
    }

    public function setPool(Pool $pool): self
    {
        $this->pool = $pool;
        return $this;
    }

    protected function __construct()
    {
        if (!is_dir(Pool::getProcessDir())) {
            mkdir(Pool::getProcessDir(), 0777, true);
        }
    }

    public function start()
    {
        $this->setPool(new Pool());
        $this->getPool()->start();
    }

    public function dispatch(AbstractJob $job)
    {
        $this->getPool()->getQueue()->dispatch($job);
    }

    public function getProcess()
    {
        return $this->getPool()->getProcess();
    }

    public function stop($signal)
    {
        if (($signal < 1 || $signal > 31) && $signal != 99) {
            echo sprintf("signal must in 1 between 31,given int(%s)\n", $signal);
            exit;
        }
        $pids = $this->getPidByQueueName(true);
        if (empty($pids)) {
            echo sprintf("empty pids!\n");
            return;
        }
        foreach ($pids as $pid) {
            $pid = (int)$pid;
            if (false === posix_kill($pid, $signal == 99 ? 9 : $signal)) {
                echo sprintf("failed to kill the process, pid:%s\n", $pid);
                continue;
            }
            echo sprintf("success killed, pid:%s\n", $pid);
        }
        Pool::killSubProcess();
    }

    public function getPidByQueueName($force = false)
    {
        $ret = shell_exec($cmd = "ps -ef|grep -v grep|grep AsyncDispatch|awk '{print " . ($force ? '$2' : '$3') . "}'");
        if (!preg_match("/[0-9\n]+/", $ret)) {
            return false;
        }
        debug($cmd);
        return array_unique(array_diff(explode("\n", $ret), ["", "1"]));
    }
}
