<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Server\Queue;


use AsyncDispatch\FromAlgoJob;
use AsyncDispatch\FromAlgoAlgoData;
use AsyncDispatch\Server\Queue\Kafka\ConsumerRdkafka;
use AsyncDispatch\AbstractJob;

class Kafka extends Abs
{
    protected $kafkaConsumer = null;

    protected function __construct(?string $queueName = null)
    {
        parent::__construct($queueName);
        $this->setKafkaConsumer($queueName);
    }

    public function getKafkaConsumer(): ConsumerRdkafka
    {
        return $this->kafkaConsumer;
    }

    public function setKafkaConsumer(string $queueName)
    {
        $this->kafkaConsumer = ConsumerRdkafka::getInstance($queueName);
    }

    public function pop(): ?AbstractJob
    {
        $job = $this->getKafkaConsumer()->pop(60);
        $this->beforeParse($job);
        if (!empty($job)) {
            $job = new FromAlgoJob(new FromAlgoAlgoData($job, $this->getKafkaConsumer()->getTopic(), 0));
            $this->afterParse($job);
        }
        return $job;
    }
}
