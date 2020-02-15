<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-14
 * Time: 21:29
 */


namespace AsyncDispatch\Tests\AsyncDispatch\Queues\Kafka;


use AsyncDispatch\Queues\Kafka\ConsumerKafka;
use PHPUnit\Framework\TestCase;

class ConsumerKafkaTest extends TestCase
{
    /**
     * @test
     */
    public function pop()
    {
        ConsumerKafka::getInstance()->pop(3);
    }
}
