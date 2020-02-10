<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/26/2019
 * Time: 10:37 AM
 */

namespace AsyncDispatch\Server\Queue\Kafka;

use AsyncDispatch\Server\Queue\Kafka\Lite\SuperConsumer;
use JimLog\Config;

class ConsumerRdkafka
{
    protected static $instance  = [];
    protected        $liteKafka = null;
    protected        $config    = null;
    protected        $topic     = null;
    protected        $consumer  = null;

    public function getConsumer(): ?SuperConsumer
    {
        return $this->consumer;
    }

    public function setConsumer(SuperConsumer $consumer): void
    {
        $this->consumer = $consumer;
    }

    public function getLiteKafka(): Lite
    {
        return $this->liteKafka;
    }

    public function setLiteKafka(Lite $liteKafka): void
    {
        $this->liteKafka = $liteKafka;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(?Config $config): void
    {
        $this->config = $config;
    }

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic): void
    {
        $this->topic = $topic;
    }

    public static function getInstance($topic, $channel = 'default')
    {
        if (!isset(static::$instance[$channel])) {
            static::$instance[$channel] = new static($topic);
        }
        return static::$instance[$channel];
    }

    protected function __construct(string $topic)
    {
        $this->setTopic(substr($topic, 6));
        $this->setConfig(Config::kafka());
        $this->setLiteKafka(new Lite($this->getConfig()->get('kafka.host')));
    }

    public function pop($timeout): ?string
    {
        if (null == $this->getConsumer()) {
            $this->aliveConsumer();
        }
        $message = $this->getConsumer()->consume($timeout);
        if (!empty($message) && !empty($message->payload)) {
            return $message->payload;
        }
        return null;
    }

    protected function aliveConsumer()
    {
        $topic   = $this->getConfig()->get('kafka.topic_return');
        $groupId = $this->getConfig()->get('kafka.group_id');
        $this->setConsumer($this->getLiteKafka()->newSuperConsumer($topic, $groupId));
    }
}
