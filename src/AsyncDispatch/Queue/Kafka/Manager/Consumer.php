<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\AsyncDispatch\Queue\Kafka\Manager;

use AsyncDispatch\AsyncDispatch\Queue\Kafka\Manager;
use AsyncDispatch\Instance;
use RdKafka\Conf;
use RdKafka\Consumer as RdKafkaConsumer;
use RdKafka\ConsumerTopic;
use RdKafka\TopicConf;

class Consumer
{
    use Instance;
    protected $topic     = null;
    protected $timeout   = 120;//s
    protected $partition = 0;
    protected $consumer  = null;
    protected $manager   = null;

    public function getManager(): Manager
    {
        return $this->manager;
    }

    public function setManager($manager): self
    {
        $this->manager = $manager;
        return $this;
    }

    public function getTopic(): ConsumerTopic
    {
        return $this->topic;
    }

    public function setTopic(ConsumerTopic $topic): void
    {
        $this->topic = $topic;
    }

    public function getPartition(): int
    {
        return $this->partition;
    }

    public function setPartition(Int $partition): void
    {
        $this->partition = $partition;
    }

    public function getConsumer(): ?RdKafkaConsumer
    {
        return $this->consumer;
    }

    public function setConsumer(?RdKafkaConsumer $consumer): void
    {
        $this->consumer = $consumer;
    }

    protected function __construct(Manager $manager)
    {
        $this->setConsumer(new RdKafkaConsumer($this->getKafKaConf()));
        $this->getConsumer()->addBrokers($manager->getBrokerList());
        $this->setTopic($this->getConsumer()->newTopic($manager->getTopic(), $this->getTopicConf()));
        $this->setManager($manager);
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function consumerStart($partition = 0, $offset = RD_KAFKA_OFFSET_END)
    {
        $this->setPartition($partition);
        $this->getTopic()->consumeStart($this->partition, $offset);
    }

    public function consumerStop()
    {
        $this->getTopic()->consumeStop($this->partition);
    }

    public function consume()
    {
        $message = $this->getTopic()->consume($this->getPartition(), $this->getTimeout() * 1000);
        if (empty($message)) return null;
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return $message;
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                break;
            default:
                throw new \Exception($message->errstr(), $message->err);
                break;
        }
        return false;
    }

    public function consumeMultiTopic(...$topics)
    {
        $queue = $this->getConsumer()->newQueue();
        foreach (array_merge($topics, $this->getManager()->getTopic()) as $topic) {
            $this->getConsumer()->newTopic($topic, $this->getTopicConf())->consumeQueueStart(0, RD_KAFKA_OFFSET_BEGINNING, $queue);
        }
        $message = $queue->consume($this->getTimeout() * 1000);
        if (empty($message)) return null;
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return $message;
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                break;
            default:
                throw new \Exception($message->errstr(), $message->err);
                break;
        }
        return false;
    }

    public function getMassage($partition, $maxSize, $offset = RD_KAFKA_OFFSET_STORED)
    {
        $retList = array();
        $this->consumerStart($partition, $offset);
        for ($i = 0; $i < $maxSize; $i++) {
            $message = $this->consume();
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $retList[] = $message;
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    break 2;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
        $this->consumerStop();
        return $retList;
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
