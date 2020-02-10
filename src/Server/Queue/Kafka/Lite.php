<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch\Server\Queue\Kafka;


use AsyncDispatch\Server\Queue\Kafka\Lite\Consumer;
use AsyncDispatch\Server\Queue\Kafka\Lite\Producer;
use AsyncDispatch\Server\Queue\Kafka\Lite\SuperConsumer;

class Lite
{
    protected $brokerList = "";
    protected $topic      = "";
    protected $groupId    = "";
    protected $partition  = 0;

    public function getGroupId(): string
    {
        return $this->groupId;
    }

    public function setGroupId(string $groupId): void
    {
        $this->groupId = $groupId;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic($topic): void
    {
        $this->topic = $topic;
    }

    public function setBrokerList(string $brokerList): void
    {
        $this->brokerList = $brokerList;
    }

    public function getBrokerList(): string
    {
        return $this->brokerList;
    }

    public function __construct($brokerList)
    {
        $this->brokerList = $brokerList;
        $this->ping();
    }

    public function newProducer()
    {
        return Producer::getInstance($this, $this->getTopic());
    }

    public function newConsumer()
    {
        return Consumer::getInstance($this);
    }

    public function newSuperConsumer($topic, $groupId)
    {
        $this->setTopic($topic);
        $this->setGroupId($groupId);
        return SuperConsumer::getInstance($this);
    }

    private function checkTCP($ip, $port)
    {
        $sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_set_nonblock($sock);
        socket_connect($sock, $ip, $port);
        socket_set_block($sock);
        $r = array($sock);
        $w = array($sock);
        $f = array($sock);
        switch (socket_select($r, $w, $f, 5)) {
            case 1:
                socket_close($sock);
                return true;
            default:
                socket_close($sock);
                return false;
        }
    }

    public function ping()
    {
        $errTCP = 0;
        $List   = explode(',', $this->brokerList);
        foreach ($List as $v) {
            $IP = explode(':', $v);
            if (!$this->checkTCP($IP[0], isset($IP[1]) ? $IP[1] : 9092)) {
                $errTCP++;
            }
        }
        if ((count($List) - $errTCP) == 0) {
            throw new \Exception("No can use KafKa");
        }
    }
}
