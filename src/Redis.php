<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDis;

use JimLog\Config;
use Predis\Client;


trait Redis
{
    public function redis()
    {
        Config::loadIni();
        static $redis = null;
        if (empty($redis)) {
            $config = Config::redis();
            $pwd    = empty($config->get('default.auth')) ? [] : ['password' => $config->get('default.auth'), 'profile' => '2.8', 'prefix' => $config->get('default.auth')];
            $params = ['database' => (int)$config->get('default.database'),];
            $params = array_merge($params, $pwd);
            $redis  = new Client($config->get('default.host'), ['parameters' => $params]);
        }
        return $redis;
    }

    public function getRedisKey($id)
    {
        return "course_v5:{$id}";
    }
}
