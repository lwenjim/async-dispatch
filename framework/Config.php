<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2020-02-12
 * Time: 23:13
 */


namespace AsyncDispatch;


use Composer\Autoload\ClassLoader;

/**
 * Class Config
 * @method static Ini kafka()
 * @method static Ini log()
 * @method static Ini redis()
 * @package AsyncDispatch
 */
class Config
{
    protected static $container = [];
    const ENV_DEV  = 'dev';
    const ENV_TEST = 'test';
    const ENV_PRE  = 'pre';
    const ENV_PROD = 'prod';
    const ENV      = 'dev';

    protected static $configs = [];

    public static function get($key)
    {
        return self::basic()->get($key);
    }

    public static function getAndImplode($key, $glue = "", $replace = '')
    {
        return self::ini()->getAndImplode($key, $glue, $replace);
    }

    protected static function cacheIni($key, Ini $ini)
    {
        self::$configs[$key] = $ini;
    }

    public static function ini($name = 'basic')
    {
        $name .= '_ini';
        if (!isset(self::$configs[$name])) {
            throw new \Exception(sprintf('Not found Config for %s', $name));
        }
        return self::$configs[$name];
    }

    public static function loadIni()
    {
        foreach (static::getUnit() as $name) {
            $configFile = self::getAppPath() . "/config/{$name}.ini";
            $key        = "{$name}_ini";
            self::cacheIni($key, new Ini($configFile));
        }
    }

    public static function getUnit()
    {
        return array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, array_diff(scandir(self::getAppPath() . 'config/'), ['.', '..']));
    }

    public static function getAppPath()
    {
        $filename = (new \ReflectionClass(ClassLoader::class))->getMethods()[0]->getDeclaringClass()->getFilename();
        return substr($filename, 0, strpos($filename, 'vendor/'));
    }

    public static function getEnv()
    {
        return self::ENV_DEV;
    }

    public static function __callStatic($name, $arguments)
    {
        return self::ini($name);
    }
}
