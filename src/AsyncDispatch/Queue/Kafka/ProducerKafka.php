<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/18/2019
 * Time: 10:42 AM
 */

namespace AsyncDispatch\AsyncDispatch\Queue\Kafka;

use AsyncDispatch\Config;
use AsyncDispatch\Instance;
use JimLog\Ini;

class ProducerKafka
{
    use Instance;
    protected $liteKafka = null;
    protected $config    = null;
    protected $topic     = null;

    public function getLiteKafka(): ?Manager
    {
        return $this->liteKafka;
    }

    public function setLiteKafka(?Manager $liteKafka): void
    {
        $this->liteKafka = $liteKafka;
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
        $this->setLiteKafka(new Manager(implode(',', $this->getBrokerList())));
    }

    public function send(string $data)
    {
        $topic = $this->getConfig()->get('default.topic');
        $this->getLiteKafka()->setTopic($topic);
        $Producer = $this->getLiteKafka()->newProducer();
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
