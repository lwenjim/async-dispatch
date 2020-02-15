<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Queues\Kafka\Manager;

use AsyncDispatch\Instance;
use AsyncDispatch\Queues\Kafka\Manager;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\TopicConf as RdKafkaTopicConf;
class SuperConsumer
{
    use Instance;
    protected $socketTimeoutMs      = 1200;
    protected $autoCommitIntervalMs = 1000;
    protected $fetchWaitMaxMs       = 200;
    protected $autoOffsetReset      = "smallest";
    protected $consumer             = null;
    protected $manager              = null;

    public function getManager(): Manager
    {
        return $this->manager;
    }

    public function setManager(Manager $manager): void
    {
        $this->manager = $manager;
    }

    public function getConsumer(): KafkaConsumer
    {
        return $this->consumer;
    }

    public function setConsumer(KafkaConsumer $consumer): void
    {
        $this->consumer = $consumer;
    }

    public function getAutoOffsetReset(): string
    {
        return $this->autoOffsetReset;
    }

    public function setAutoOffsetReset(string $autoOffsetReset): void
    {
        $this->autoOffsetReset = $autoOffsetReset;
    }

    public function getAutoCommitIntervalMs(): int
    {
        return $this->autoCommitIntervalMs;
    }

    public function setAutoCommitIntervalMs(int $autoCommitIntervalMs): void
    {
        $this->autoCommitIntervalMs = $autoCommitIntervalMs;
    }

    public function getFetchWaitMaxMs(): int
    {
        return $this->fetchWaitMaxMs;
    }

    public function setFetchWaitMaxMs(int $fetchWaitMaxMs): void
    {
        $this->fetchWaitMaxMs = $fetchWaitMaxMs;
    }

    public function getSocketTimeoutMs(): int
    {
        return $this->socketTimeoutMs;
    }

    public function setSocketTimeoutMs(int $socketTimeoutMs): void
    {
        $this->socketTimeoutMs = $socketTimeoutMs;
    }

    public function callBackRebalance(KafkaConsumer $kafka, $err, array $partitions = null)
    {
        switch ($err) {
            case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                $kafka->assign($partitions);
                break;
            case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                $kafka->assign(NULL);
                break;
            default:
                throw new \Exception($err);
        }
    }

    protected function __construct(Manager $manager)
    {
        $this->setManager($manager);
        $this->setConsumer(new KafkaConsumer($this->getKafkaConf()));
        $this->getConsumer()->subscribe([$this->getManager()->getTopic()]);
    }

    protected function getKafkaConf()
    {
        static $conf = null;
        if (null == $conf) {
            $conf = new Conf();
            $conf->setRebalanceCb([$this, 'callBackRebalance']);
            $conf->set('group.id', $this->getManager()->getGroupId());
            $conf->set('metadata.broker.list', $this->getManager()->getBrokerList());
            $conf->set('socket.timeout.ms', $this->getSocketTimeoutMs());
            $conf->set('fetch.wait.max.ms', $this->getFetchWaitMaxMs());
            if (function_exists('pcntl_sigprocmask')) {
                pcntl_sigprocmask(SIG_BLOCK, array(SIGIO));
                $conf->set('internal.termination.signal', SIGIO);
            } else {
                $conf->set('Queues.buffering.max.ms', 1);
            }
            @$conf->setDefaultTopicConf($this->getTopicConf());
        }
        return $conf;
    }

    protected function getTopicConf()
    {
        $topicConf = null;
        if (null == $topicConf) {
            $topicConf = new RdKafkaTopicConf();
            $topicConf->set('auto.commit.interval.ms', $this->getAutoCommitIntervalMs());
            $topicConf->set('auto.offset.reset', $this->getAutoOffsetReset());
        }
        return $topicConf;
    }

    public function consume($timeout)
    {
        $message = $this->getConsumer()->consume($timeout * 1000);
        if (empty($message)) return false;
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                return $message;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                debug("No more messages; will wait for more");
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                debug("Timed out for kafka-queue");
                break;
            default:
                throw new \Exception($message->errstr(), $message->err);
        }
        return false;
    }
}
