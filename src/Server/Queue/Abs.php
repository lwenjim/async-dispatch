<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Server\Queue;

use AsyncDispatch\AbstractJob;
use JimLog\Config;

abstract class Abs
{
    protected static $instance = [];
    public const QUEUE_TYPE_REDIS = 1;
    public const QUEUE_TYPE_KAFKA = 2;
    public const QUEUE_TYPE       = [
        self::QUEUE_TYPE_REDIS => 'redis',
        self::QUEUE_TYPE_KAFKA => 'kafka',
    ];

    protected $queueName = null;

    protected $preQueueName = 'dispatch-producer-consumer:Queue:';

    protected function __construct(?string $queueName = null)
    {
        $this->preQueueName = Config::redis()->get('pool.key.pre');
        $this->setQueueName($queueName);
    }

    public static function getInstance(string $queueName = null)
    {
        if (empty(static::$instance[$queueName])) {
            static::$instance[$queueName] = new static($queueName);
        }
        return static::$instance[$queueName];
    }

    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    public function setQueueName(?string $queueName): void
    {
        $this->queueName = strtolower($queueName);
    }

    abstract public function pop(): ?AbstractJob;

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
}
