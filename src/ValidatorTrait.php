<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 5/4/2019
 * Time: 1:33 PM
 */

namespace AsyncDis;

use Particle\Validator\Validator as ToolValidator;

trait ValidatorTrait
{
    protected $validator = null;

    public function getValidator(): ?ToolValidator
    {
        return $this->validator;
    }

    public function setValidator(?ToolValidator $validator)
    {
        $this->validator = $validator;
        return $this;
    }
}
