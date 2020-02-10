<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/18/2019
 * Time: 8:49 PM
 */

namespace AsyncDispatch;

use Particle\Validator\ValidationResult;
use Particle\Validator\Validator;

abstract class AbstractAlgoData
{
    use ValidatorTrait;
    protected const PROJECT_TYPE        = [0, 1, 2, 3, 4, 5, 6];
    public const    OPERATE_TYPE_ADD    = 'ADD';
    public const    OPERATE_TYPE_DELETE = 'DELETE';
    public const    OPERATE_TYPE_UPDATE = 'UPDATE';
    public const    OPERATE_TYPE        = [
        self::OPERATE_TYPE_ADD,
        self::OPERATE_TYPE_DELETE,
        self::OPERATE_TYPE_UPDATE,
    ];
    public    $op;
    protected $result = null;

    public function getResult(): ?ValidationResult
    {
        return $this->result;
    }

    public function setResult(?ValidationResult $result): void
    {
        $this->result = $result;
    }

    public function getOp()
    {
        return $this->op;
    }

    public function setOp($op): void
    {
        $this->op = strtoupper($op);
    }

    public function __construct(string $op)
    {
        $this->setOp($op);
        $this->setValidator(new Validator);
        $this->getValidator()->required('op')->inArray(self::OPERATE_TYPE);
    }

    public function validate(): bool
    {
        $this->setResult($this->getValidator()->validate($this->toArray()));
        return $this->getResult()->isValid();
    }

    public function toArray()
    {
        return json_decode(json_encode($this), true);
    }
}
