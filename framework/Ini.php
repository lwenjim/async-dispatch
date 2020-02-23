<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-08
 * Time: 14:18
 */


namespace AsyncDispatch;


class Ini
{
    protected $configFile;
    protected $data;

    public function __construct($configFile, $channel = 'default')
    {
        $this->configFile = $configFile;
        $data             = parse_ini_file($configFile, true);
        $this->data       = $data['common'] + $data[Config::ENV . ':common'];
    }

    public function getData()
    {
        return $this->data;
    }

    public function __debugInfo()
    {
        return $this->getData();
    }

    public function __get($name)
    {
        return $this->__debugInfo()[$name];
    }

    public function get($key)
    {
        if (empty($key)) return null;
        if (is_string($key)) {
            return $this->data[$key];
        } else if (is_array($key)) {
            $arr = [];
            foreach ($key as $subKey) {
                $arr[$subKey] = $this->get($subKey);
            }
            return $arr;
        }
        return null;
    }

    public function getAndImplode($key, $glue = "", $replace = '')
    {
        $str = implode($glue, $this->get($key));
        if ($replace != '') {
            return sprintf($str, $replace);
        }
        return $str;
    }
}
