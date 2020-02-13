<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/26/2019
 * Time: 10:37 AM
 */

namespace AsyncDispatch\AsyncDispatch\Queue\Kafka;

use AsyncDispatch\Config;
use AsyncDispatch\Instance;
use AsyncDispatch\AsyncDispatch\Queue\Kafka\Manager\SuperConsumer;
use JimLog\Ini;

class ConsumerRdkafka
{
    use Instance;
    protected $liteKafka = null;
    protected $config    = null;
    protected $topic     = null;
    protected $consumer  = null;

    public function getConsumer(): ?SuperConsumer
    {
        return $this->consumer;
    }

    public function setConsumer(SuperConsumer $consumer): void
    {
        $this->consumer = $consumer;
    }

    public function getLiteKafka(): Manager
    {
        return $this->liteKafka;
    }

    public function setLiteKafka(Manager $liteKafka): void
    {
        $this->liteKafka = $liteKafka;
    }

    public function getConfig(): ?Ini
    {
        return $this->config;
    }

    public function setConfig(?Ini $config): void
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

    protected function __construct(string $topic)
    {
        $this->setTopic(substr($topic, 6));
        $this->setConfig(Config::kafka());
        $this->setLiteKafka(new Manager($this->getConfig()->get('default.host')));
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
        $topic   = $this->getConfig()->get('default.topic_return');
        $groupId = $this->getConfig()->get('default.group_id');
        $this->setConsumer($this->getLiteKafka()->newSuperConsumer($topic, $groupId));
    }
}
