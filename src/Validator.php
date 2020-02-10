<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2019-07-21
 * Time: 00:21
 */

namespace AsyncDis;


use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;

trait Validator
{
    public function validator()
    {
        static $validator = null;
        if (empty($validator)) {
            $validator = new Factory(new Translator(new ArrayLoader(), 'Translator'));
        }
        return $validator;
    }

    public function getValidatorError($validator)
    {
        $errors = $validator->errors()->toArray();
        foreach ($errors as $k => $v){
            $message[] = "{$k}:" . $v[0];
        }

        return implode("--", $message ?? []);
    }
}
