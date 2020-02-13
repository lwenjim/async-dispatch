<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Server;

use AsyncDispatch\Server\Queue\Abs;
use AsyncDispatch\Server\Queue\Factory;
use AsyncDispatch\Config;
use Swoole\Process\Pool as ProcessPool;

class Pool
{
    protected        $pool            = null;
    protected        $procNum         = null;
    protected        $queue           = null;
    protected        $masterProcessId = null;
    protected        $process         = [];
    protected static $action          = ['workerStart', 'workerStop'];
    protected        $block           = [];

    public function getProcNum(): ?int
    {
        return $this->procNum;
    }

    public function setProcNum(?int $procNum): self
    {
        $this->procNum = $procNum;
        return $this;
    }

    public function getQueue(): ?Abs
    {
        return $this->queue;
    }

    public function setQueue(?Abs $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function getMasterProcessId(): ?int
    {
        return $this->masterProcessId;
    }

    public function setMasterProcessId(?int $masterProcessId): self
    {
        $this->masterProcessId = $masterProcessId;
        return $this;
    }

    public function setBlock(int $pid, bool $block): void
    {
        $this->block[$pid] = $block;
    }

    public function getBlock($pid): bool
    {
        return $this->block[$pid];
    }

    public function setBlocking(int $pid, bool $blocking): void
    {
        $this->setBlock($pid, $blocking);
        File::getInstance(self::getProcessDir() . $pid)->open('w+')->write($blocking ? 1 : 2)->flush();
    }

    public static function getBlocking(int $pid): int
    {
        return File::getInstance(self::getProcessDir() . $pid)->content();
    }

    public static function killSubProcess()
    {
        $fileList = Directory::getInstance(self::getProcessDir())->scan();
        if (empty($fileList)) {
            return null;
        }
        array_filter($fileList, function (File $file) {
            if (1 != $file->content()) {
                return false;
            }
            $pid = (int)pathinfo($file->getFilename(), PATHINFO_BASENAME);
            debug($pid . '-' . $file->content() . '-' . $file->getFilename() . "\n");
            return posix_kill($pid, SIGKILL);
        });
        if (empty($fileList)) return;
        array_map(function (File $file) {
            @$file->unlink();
        }, $fileList);
    }

    public function __construct()
    {
        $this->setQueue(Factory::factory());
        $this->setMasterProcessId(posix_getpid());
        $this->setPool(new ProcessPool((int)Config::get('queue.num'), 0, 0));
        self::bind();
    }

    public function workerStart(ProcessPool $pool, $workerId)
    {
        $this->_workerStart();
    }

    protected function _workerStart()
    {
        pcntl_async_signals(true);
        $pid     = posix_getpid();
        $running = true;
        pcntl_signal(SIGTERM, function () use (&$running, $pid) {
            $running = false;
            if (true == $this->getBlock($pid)) {
                posix_kill($pid, SIGKILL);
            }
        });
        while ($running) {
            try {
                pcntl_signal_dispatch();
                $this->setBlocking($pid, true);
                $job = $this->getQueue()->pop();
                $this->setBlocking($pid, false);
                if (null == $job) {
                    sleep(1);
                    continue;
                }
                $job->beforeHandle();
                $job->handle();
                $job->afterHandle();
            } catch (\Exception | \Error $exception) {
                if (isset($job)) {
                    $job->exceptionHandle($exception);
                    unset($job);
                } else {
                    debug($exception->getMessage(), 'no-job-exception');
                }
                sleep(1);
            }
        }
    }

    public function getPool(): ?ProcessPool
    {
        return $this->pool;
    }

    public function setPool(?ProcessPool $pool): self
    {
        $this->pool = $pool;
        return $this;
    }

    public function workerStop(ProcessPool $pool, $workerId)
    {
        debug(sprintf("spid:%d, mpid:%d, Worker#%d is stopped\n", posix_getpid(), $this->masterProcessId, $workerId));
    }

    public function bind()
    {
        foreach (self::$action as $action) {
            $this->getPool()->on(ucfirst($action), [$this, $action]);
        }
    }

    public function getProcess()
    {
        return $this->getPool()->getProcess();
    }

    public function start()
    {
        return $this->getPool()->start();
    }

    public static function getProcessDir()
    {
        return Config::getAppPath().'/runtime/log/pid/';
    }
}
