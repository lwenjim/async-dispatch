<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDis\Server\Queue\Kafka\Lite;


use AsyncDis\Server\Queue\Kafka\Lite;
use RdKafka\Conf;
use RdKafka\Producer as RdKafkaProducer;
use RdKafka\ProducerTopic;
use RdKafka\TopicConf;

class Producer
{
    protected static $instance = [];
    protected        $topic;

    public function getTopic(): ProducerTopic
    {
        return $this->topic;
    }

    public function setTopic(ProducerTopic $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    protected function __construct(Lite $kafKaLite)
    {
        $producer = new RdKafkaProducer($this->getKafKaConf());
        $producer->addBrokers($kafKaLite->getBrokerList());
        $this->setTopic($producer->newTopic($kafKaLite->getTopic(), $this->getTopicConf()));
    }

    public function setMessage($partition, $value, $key = null)
    {
        $this->getTopic()->produce($partition, 0, $value, $key);
    }

    protected function getTopicConf()
    {
        $conf = null;
        if (null == $conf) {
            $conf = new TopicConf();
        }
        return $conf;
    }

    protected function getKafKaConf()
    {
        $conf = null;
        if (null == $conf) {
            $conf = new Conf();
        }
        return $conf;
    }

    public static function getInstance(Lite $liteKafka, $channel = 'default')
    {
        if (!isset(static::$instance[$channel])) {
            static::$instance[$channel] = new static($liteKafka);
        }
        return static::$instance[$channel];
    }
}

