<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\AsyncDispatch\Queue;


use AsyncDispatch\AsyncDispatch\Queue\Kafka\ConsumerRdkafka;
use AsyncDispatch\AsyncDispatch\Queue\Kafka\ProducerKafka;

class Kafka extends Abs
{
    protected $kafkaConsumer = null;
    protected $kafkaProduct  = null;

    public function getKafkaProduct(): ProducerKafka
    {
        return $this->kafkaProduct;
    }

    public function setKafkaProduct($kafkaProduct): void
    {
        $this->kafkaProduct = $kafkaProduct;
    }

    protected function __construct(?string $queueName = null)
    {
        parent::__construct($queueName);
        $this->setKafkaConsumer($queueName);
        $this->setKafkaProduct(ProducerKafka::getInstance());
    }

    public function getKafkaConsumer(): ConsumerRdkafka
    {
        return $this->kafkaConsumer;
    }

    public function setKafkaConsumer(string $queueName)
    {
        $this->kafkaConsumer = ConsumerRdkafka::getInstance($queueName);
    }

    public function getValue(): string
    {
        return (string)$this->getKafkaConsumer()->pop(60);
    }

    public function push($data)
    {
        $this->getKafkaProduct()->send($data);
    }
}
