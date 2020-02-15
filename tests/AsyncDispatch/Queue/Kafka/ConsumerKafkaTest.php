<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-14
 * Time: 21:29
 */


namespace AsyncDispatch\Tests\AsyncDispatch\Queue\Kafka;


use AsyncDispatch\Queue\Kafka\ConsumerKafka;
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
