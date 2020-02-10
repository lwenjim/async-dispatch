<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Server\Queue;


use AsyncDispatch\AbstractJob;
use AsyncDispatch\Redis as Cache;
use Predis\Client as Predis;

class Redis extends Abs
{
    use Cache;
    protected $redis = null;

    public function getRedis(): Predis
    {
        if (null == $this->redis) {
            $this->setRedis();
        }
        return $this->redis;
    }

    public function setRedis()
    {
        $this->redis = $this->redis();
    }

    public function pop($handle = 'brpop'): ?AbstractJob
    {
        return $this->$handle();
    }

    protected function brpop()
    {
        $serializeValue = $this->getRedis()->brpop([$this->getPreQueueName() . $this->getQueueName()], 0);
        debug("consumer message");
        if (empty($serializeValue)) {
            debug("serializeValue is empty");
            return null;
        }
        if (!isset($serializeValue[1])) {
            debug(sprintf("empty serializeValue[1]"));
            return null;
        }
        if (empty($job = unserialize(base64_decode($serializeValue[1])))) {
            debug($serializeValue, 'failed-unserialize');
            return null;
        }
        if (!($job instanceof AbstractJob)) {
            debug(sprintf("failed instance of AbstractJob"));
            return null;
        }
        $this->beforeParse($serializeValue[1]);
        $this->afterParse($job);
        return $job;
    }

    protected function rpop()
    {
        $serializeValue = $this->getRedis()->rpop($this->getPreQueueName() . $this->getQueueName());

        if (empty($serializeValue)) {
            debug("serializeValue is empty");
            return null;
        }
        if (empty($job = unserialize(base64_decode($serializeValue)))) {
            debug($serializeValue, 'failed-unserialize');
            return null;
        }
        if (!($job instanceof AbstractJob)) {
            debug(sprintf("failed instance of AbstractJob"));
            return null;
        }
        $this->beforeParse($serializeValue);
        $this->afterParse($job);
        return $job;
    }
}
