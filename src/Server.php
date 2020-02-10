<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDis;

use AsyncDis\Server\Pool;
use AsyncDis\Server\Queue\Abs;
use AsyncDis\Server\Queue\Factory;

class Server
{
    protected static $instance = [];
    protected        $queue    = null;
    protected        $pool     = null;

    public function getPool(): Pool
    {
        return $this->pool;
    }

    public function setPool(Pool $pool): self
    {
        $this->pool = $pool;
        return $this;
    }

    public function getQueue(): ?Abs
    {
        return $this->queue;
    }

    public function setQueue(Abs $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public static function getInstance($queueName = 'redis_dispatch')
    {
        if (empty(self::$instance[$queueName])) {
            self::$instance[$queueName] = new self($queueName);
        }
        return self::$instance[$queueName];
    }

    protected function __construct($queueName)
    {
        $this->setQueue(Factory::factory($queueName));
        if (!is_dir(Pool::getProcessDir())) {
            mkdir(Pool::getProcessDir(), 0777, true);
        }
        $this->checkEnv();
    }

    public function run($procNum)
    {
        $this->setPool(new Pool($this->getQueue(), $procNum));
        $this->getPool()->start();
    }

    public function dispatch(AbstractJob $job): int
    {
        $redisKey = $this->getQueue()->getPreQueueName() . $this->getQueue()->getQueueName();
        if ($job->getTried() > 0 && $job->getTried() < $job->getTries()) {
            $redisKey .= $this->getQueue()->getSuffixQueueName();
        }
        return $this->getQueue()->getRedis()->lPush($redisKey, base64_encode(serialize($job)));
    }

    public function getProcess()
    {
        return $this->getPool()->getProcess();
    }

    public function closePool($signal)
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
        $queueName = $this->getQueue()->getQueueName();
        $ret       = shell_exec($cmd = "ps -ef|grep /process/pool/start/queueName/|grep -v grep|grep /{$queueName}/|grep ".basename(APP_PATH)."|awk '{print " . ($force ? '$2' : '$3') . "}'");
        if (!preg_match("/[0-9\n]+/", $ret)) {
            return false;
        }
        debug($cmd);
        return array_unique(array_diff(explode("\n", $ret), ["", "1"]));
    }

    public function jobResolveReturn($serializeValue)
    {
        if (empty($serializeValue)) {
            return false;
        }
        if (!isset($serializeValue[1])) {
            return false;
        }
        if (empty($job = unserialize($serializeValue[1]))) {
            return false;
        }
        if (!($job instanceof AbstractJob)) {
            debug(sprintf("failed instance of AbstractJob"));
            return false;
        }
        return $job;
    }

    public function checkEnv()
    {
        if (!function_exists('shell_exec')) {
            throw new \Exception('not exists the function of shell_exec ');
        }
    }
}
