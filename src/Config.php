<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-12
 * Time: 23:13
 */


namespace AsyncDispatch;


/**
 * Class Config
 * @method static \JimLog\Ini kafka()
 * @method static \JimLog\Ini log()
 * @method static \JimLog\Ini redis()
 * @package AsyncDispatch
 */
class Config extends \JimLog\Config
{
    protected static $container = [];

    public static function app(string $key = null, $instance = null)
    {
        if (empty($key)) {
            return self::$container;
        }
        if (empty($instance)) {
            $alias = array_flip(self::getAlias());
            if (isset($alias[$key])) {
                $key = $alias[$key];
            }
            return isset(self::$container[$key]) ? self::$container[$key] : false;
        }
        return self::$container[$key] = $instance;
    }

    protected static function getAlias()
    {
        return [

        ];
    }
}
