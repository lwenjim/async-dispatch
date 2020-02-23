<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Queues;

use AsyncDispatch\AbstractJob;
use AsyncDispatch\Instance;
use AsyncDispatch\Config;

abstract class Abs
{

    public const QUEUE_TYPE_REDIS = 1;
    public const QUEUE_TYPE_KAFKA = 2;
    public const QUEUE_TYPE       = [
        self::QUEUE_TYPE_REDIS => 'redis',
        self::QUEUE_TYPE_KAFKA => 'kafka',
    ];

    protected $queueName = null;

    protected $preQueueName = null;

    protected function __construct()
    {
        $this->setQueueName('');
        $this->setPreQueueName((string)Config::get('queue.redis.preKey'));
    }

    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    public function setQueueName(?string $queueName): void
    {
        $this->queueName = strtolower($queueName);
    }

    abstract protected function getValue(): string;

    public function pop(): ?AbstractJob
    {
        $value = $this->getValue();
        if (empty($value) || empty($job = unserialize(base64_decode($value))) || !($job instanceof AbstractJob)) {
            debug($value, 'FailedUnserialize');
            return null;
        }
        $this->afterParse($job);
        return $job;
    }

    public function getPreQueueName(): string
    {
        return $this->preQueueName;
    }

    public function setPreQueueName(string $preQueueName): void
    {
        $this->preQueueName = $preQueueName;
    }

    public function beforeParse($obj)
    {

    }

    public function afterParse($obj)
    {

    }

    abstract public function push(string $job);

    public function dispatch(AbstractJob $job)
    {
        if ($job->getTried() > 0 && $job->getTried() > $job->getTries()) {
            throw new \Exception(sprintf('times out'));
        }
        $this->push($job->toString());
    }

    abstract function commit();
}
