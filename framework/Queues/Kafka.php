<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Queues;


use AsyncDispatch\Instance;
use AsyncDispatch\Queues\Kafka\ConsumerKafka;
use AsyncDispatch\Queues\Kafka\ProducerKafka;

class Kafka extends Abs
{
    use Instance;
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

    protected function __construct()
    {
        parent::__construct();
        $this->setKafkaConsumer();
        $this->setKafkaProduct(ProducerKafka::getInstance());
    }

    public function getKafkaConsumer(): ConsumerKafka
    {
        return $this->kafkaConsumer;
    }

    public function setKafkaConsumer()
    {
        $this->kafkaConsumer = ConsumerKafka::getInstance();
    }

    public function getValue(): string
    {
        return (string)$this->getKafkaConsumer()->pop(60);
    }

    public function push($data)
    {
        $this->getKafkaProduct()->send($data);
    }

    public function commit()
    {
        $this->getKafkaConsumer()->getConsumer()->commit();
    }
}
