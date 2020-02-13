<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\AsyncDispatch\Queue;


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

    protected function getValue(): string
    {
        $serializeValue = $this->getRedis()->brpop([$this->getPreQueueName() . $this->getQueueName()], 0);
        if (empty($serializeValue)) {
            debug("serializeValue is empty");
            return null;
        }
        if (!isset($serializeValue[1])) {
            debug(sprintf("empty serializeValue[1]"));
            return null;
        }
        $this->beforeParse($serializeValue[1]);
        return $serializeValue[1];
    }

    public function push(string $data)
    {
        $redisKey = $this->getPreQueueName() . $this->getQueueName();
        $this->getRedis()->lPush($redisKey, [$data]);
    }
}
