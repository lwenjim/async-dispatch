<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/22/2019
 * Time: 8:10 PM
 */

namespace AsyncDispatch;


class FromAlgoJob extends AbstractJob
{
    protected $reasonKey = 'reason';
    protected $statusKey = 'status';
    protected const           KEYMAP = [
        'RULE'            => 'ruleId',
        'SECTION'         => 'sectionId',
        'LO'              => 'loCode',
        'CONTENT_MAP'     => 'resId',
        'RES_VIDEO'       => 'resId',
        'ALGO_MODEL'      => 'algoCode',
        'IRS_HIGH_COURSE' => 'course',
    ];

    protected const           STATUS2INT = [
        'success' => 1,
        'error'   => 0
    ];

    public function getAlgoData(): ?FromAlgoAlgoData
    {
        return $this->algoData;
    }

    public function setAlgoData(?FromAlgoAlgoData $algoData): void
    {
        $this->algoData = $algoData;
    }

    public function getReasonKey(): string
    {
        return $this->reasonKey;
    }

    public function setReasonKey(string $reasonKey): void
    {
        $this->reasonKey = $reasonKey;
    }

    public function __construct(FromAlgoAlgoData $algoData)
    {
        parent::__construct($algoData);
    }

    protected function log($message)
    {
        $data = ['topic' => $this->getAlgoData()->getTopic(), 'part' => $this->getAlgoData()->getPart(), 'message' => $message];
        debug(\GuzzleHttp\json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    protected function check($jsonDecode)
    {
        if (!isset($jsonDecode['objectType']) || !isset($jsonDecode['action']) || !isset($jsonDecode['data'])) {
            throw new \Exception(sprintf('miss field for objectType, action and data, returnData:%s', json_encode($jsonDecode)));
        }
        if (!array_key_exists($jsonDecode['objectType'], static::KEYMAP)) {
            throw new \Exception(sprintf('not found mapping of objectType field, data:%s', json_encode($jsonDecode)));
        }
    }

    public function handle()
    {
        $value = $this->getAlgoData()->getKafkaValue();
        debug($value);
        $jsonDecode = json_decode($value, true);
        $this->check($jsonDecode);
        $params = [];
        foreach ($jsonDecode['data'] as $row) {
            if (!isset(static::KEYMAP[$jsonDecode['objectType']])) {
                $this->log(sprintf("error STATUS2INT, value information:%s", json_encode($row)));
                continue;
            }
            $objectTypeKey = static::KEYMAP[$jsonDecode['objectType']];
            if (!isset($row[$objectTypeKey]) || !isset($row[$this->statusKey]) || !isset($row[$this->reasonKey])) {
                $this->log(sprintf("miss field for {$objectTypeKey}|{$this->statusKey}|$this->reasonKey, value information:%s", json_encode($row)));
                continue;
            }
            $params[] = [
                'uuid'        => str_pad(str_replace(".", rand(10, 99), microtime(true)), 16, '0'),
                'message'     => $row[$this->reasonKey],
                'data_id'     => $row[$objectTypeKey],
                'data_type'   => $jsonDecode['objectType'],
                'data_status' => static::STATUS2INT[$row[$this->statusKey]],
                'autoutime'   => date('Y-m-d H:i:s'),
            ];
        }
        $this->saveDb($params);
        $this->setSuccess();
    }

    protected function saveDb($params)
    {
        debug($params);
    }
}
