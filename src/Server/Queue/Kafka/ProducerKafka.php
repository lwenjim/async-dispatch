<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/18/2019
 * Time: 10:42 AM
 */

namespace AsyncDis\Server\Queue\Kafka;

use AsyncDis\Instance;
use JimLog\Config;

class ProducerKafka
{
    use Instance;
    protected $liteKafka = null;
    protected $config    = null;
    protected $topic     = null;

    public function getTopic()
    {
        return $this->topic;
    }

    public function setTopic($topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getLiteKafka(): ?Lite
    {
        return $this->liteKafka;
    }

    public function setLiteKafka(?Lite $liteKafka): void
    {
        $this->liteKafka = $liteKafka;
    }

    public function getConfig(): ?Config
    {
        return $this->config;
    }

    public function setConfig(?Config $config): void
    {
        $this->config = $config;
    }

    protected function __construct()
    {
        $this->setConfig(Config::kafka());
        $this->setLiteKafka(new Lite(implode(',', $this->getBrokerList())));
    }

    public function send(array $objMessage)
    {
        $topic = $this->getTopic();
        $this->getLiteKafka()->setTopic($topic);
        $Producer = $this->getLiteKafka()->newProducer();
        $Producer->setMessage(0, $res = json_encode($objMessage, JSON_UNESCAPED_UNICODE));
        debug($objMessage, "ProducerKafka::send-{$objMessage['objectType']}-{$objMessage['action']}--topic--{$topic}");
    }

    protected function getBrokerList()
    {
        return array_map(function ($item) {
            list($ip, $port) = explode(":", $item);
            return $ip . ':' . ($port ? $port : $this->getConfig()->get('kafka.port'));
        }, explode(",", $this->getConfig()->get('kafka.host')));
    }

    public static function sendAlgo(array $objMessage)
    {
        $map   = [
            'SECTION'       => 'ALE_CMS_SECTION',
            'SEGMENT_RANGE' => 'ALE_CMS_SEGMENT',
            'COURSE_LO_MAP' => 'ALE_CMS_COURSE_LO_MAP',
            'LO_MAP'        => 'ALE_CMS_LO_MAP',
            'LO'            => 'ALE_CMS_LO',
            'RES_QUESTION'  => 'ALE_CMS_RESQUESTION',
            'RES_VIDEO'     => 'ALE_CMS_RESVIDEO'
        ];
        if (!isset($map[$objMessage['objectType']])) {
            throw new \Exception(sprintf('undefined %s topic', $objMessage['objectType']));
        }
        self::getInstance()->setTopic($map[$objMessage['objectType']])->send($objMessage);
    }
}
