<?php
/**
 * Created by PhpStorm.
 * User: yixue
 * Date: 4/15/2019
 * Time: 3:49 PM
 */

namespace AsyncDispatch;

use AsyncDispatch\AsyncDispatch\Queue\Factory;
use AsyncDispatch\AsyncDispatch\Queue\Kafka\ProducerKafka;

abstract class AbstractJob
{
    protected $tries     = 3;
    protected $timeout   = 60;
    protected $queueName = 'redis_theone';
    protected $jobId     = null;
    protected $tried     = 0;
    protected $parameter = null;
    protected $success   = false;
    protected $startTime = 0;

    public function getStartTime(): int
    {
        return $this->startTime;
    }

    public function setStartTime(int $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success = true): void
    {
        $this->success = $success;
    }

    public function __construct(AbstractParameter $parameter)
    {
        $this->setParameter($parameter);
        $this->setJobId(self::generateNum());
        if ($this->getTried() >= $this->getTries()) {
            throw new \Exception(sprintf("maxTimes must bigger than curTimes, maxTimes:%d, curTimes:%d", $this->getTries(), $this->getTried()));
        }
    }

    protected function generateNum()
    {
        return md5(uniqid(microtime(true), true));
    }

    public function dispatch()
    {
        Factory::factory()->dispatch($this);
    }

    public function getTries(): int
    {
        return $this->tries;
    }

    public function setTries(int $tries): void
    {
        $this->tries = $tries;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function getQueueName(): string
    {
        return $this->queueName;
    }

    public function setQueueName(string $queueName): void
    {
        $this->queueName = $queueName;
    }

    public function getJobId(): ?string
    {
        return $this->jobId;
    }

    public function setJobId(?string $jobId): void
    {
        $this->jobId = $jobId;
    }

    public function getTried(): int
    {
        return $this->tried;
    }

    public function setTried(int $tried): void
    {
        $this->tried = $tried;
    }

    abstract public function handle();

    protected function doNotifyEngine($type, $action, $object)
    {
        if (empty($type) || empty($action) || empty($object)) {
            debug(sprintf('params error for %s, param:%s', __FUNCTION__, json_encode(func_get_args())));
            return;
        }
        $data = [$object];
        if (is_array($object) && count($object) > 50) {
            $data = array_chunk($object, 50);
        }
        foreach ($data as $subject) {
            $message = array(
                "msgId"      => md5(uniqid(mt_rand(), true)),
                "batchSize"  => count($subject),
                "objectType" => $type,
                "action"     => $action,
                "data"       => $subject,
            );
            ProducerKafka::sendAlgo($message);
        }
    }

    public function beforeHandle()
    {
        $this->setStartTime(microtime(true));
    }

    public function afterHandle()
    {
        $interval = microtime(true) - $this->getStartTime();
        debug(sprintf("execute time:%s, job id:%s", $interval, $this->getJobId()));
    }

    public function exceptionHandle($exception)
    {
        error($exception->getMessage());
        while (true) {
            if ($this->getTried() >= $this->getTries() - 1) {
                error($this->getParameter(), 'times-out');
                break;
            }
            try {
                $this->beforeHandle();
                $this->handle();
                $this->afterHandle();
                if (true == $this->isSuccess()) {
                    break;
                }
            } catch (\Exception|\Error $exception) {
                error($exception->getMessage(), 'handle-exception');
            }
            $this->setTried($this->getTried() + 1);
        }
    }

    public function toString()
    {
        return base64_encode(serialize($this));
    }
}
