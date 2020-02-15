<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/18/2019
 * Time: 10:42 AM
 */

namespace AsyncDispatch\Queues\Kafka;

use AsyncDispatch\Config;
use AsyncDispatch\Instance;
use JimLog\Ini;

class ProducerKafka
{
    use Instance;
    protected $manager = null;
    protected $config  = null;
    protected $topic   = null;

    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function setManager(?Manager $manager): void
    {
        $this->manager = $manager;
    }

    public function getConfig(): ?Ini
    {
        return $this->config;
    }

    public function setConfig(?Ini $config): void
    {
        $this->config = $config;
    }

    protected function __construct()
    {
        $this->setConfig(Config::kafka());
        $this->setManager(new Manager(implode(',', $this->getBrokerList())));
    }

    public function send(string $data)
    {
        $topic = $this->getConfig()->get('default.topic');
        $this->getManager()->setTopic($topic);
        $Producer = $this->getManager()->newProducer();
        $Producer->setMessage(0, $data);
        debug($data, "ProducerKafka::send--topic--{$topic}");
    }

    protected function getBrokerList()
    {
        return array_map(function ($item) {
            list($ip, $port) = explode(":", $item);
            return $ip . ':' . ($port ? $port : $this->getConfig()->get('default.port'));
        }, explode(",", (string)$this->getConfig()->get('default.host')));
    }
}
