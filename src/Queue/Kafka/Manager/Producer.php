<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Queue\Kafka\Manager;


use AsyncDispatch\Instance;
use AsyncDispatch\Queue\Kafka\Manager;
use RdKafka\Conf;
use RdKafka\Producer as RdKafkaProducer;
use RdKafka\ProducerTopic;
use RdKafka\TopicConf;

class Producer
{
    use Instance;
    protected $topic;

    public function getTopic(): ProducerTopic
    {
        return $this->topic;
    }

    public function setTopic(ProducerTopic $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    protected function __construct(Manager $kafKaLite)
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
}

