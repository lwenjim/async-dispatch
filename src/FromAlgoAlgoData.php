<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/18/2019
 * Time: 8:45 PM
 */

namespace AsyncDispatch;

class FromAlgoAlgoData extends AbstractAlgoData
{
    public $kafkaValue;
    public $topic;
    public $part;

    public function getTopic(): ?string
    {
        return $this->topic;
    }

    public function setTopic(?string $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getPart(): ?string
    {
        return $this->part;
    }

    public function setPart(?string $part): self
    {
        $this->part = $part;
        return $this;
    }

    public function __construct(?string $kafkaValue = null, ?string $topic = null, ?string $part = null)
    {
        parent::__construct('ADD');

        $this->setKafkaValue($kafkaValue);
        $this->setTopic($topic);
        $this->setPart($part);

        $this->getValidator()->required('kafkaValue')->string();
        $this->getValidator()->required('topic')->string();
        $this->getValidator()->required('part')->string();
    }

    public function getKafkaValue()
    {
        return $this->kafkaValue;
    }

    public function setKafkaValue($kafkaValue): self
    {
        $this->kafkaValue = $kafkaValue;
        return $this;
    }
}
