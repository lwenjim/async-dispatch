<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 2019-07-19
 * Time: 10:23
 */

namespace AsyncDispatch;



use JimLog\Config;

trait Math
{
    use Redis;

    public function randomLong(int $length): int
    {
        $key = Config::get('redis.Key.math.randomLong');
        if (!$this->redis()->exists($key)) {
            $this->redis()->set($key, sprintf('%u', microtime(true) + crc32(uniqid('a', true)) << 56));
        }
        return substr($this->redis()->incr($key), 0, $length);
    }

    public function randomString(int $length): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    public function createUUID($prefix = '')
    {
        return md5(uniqid($prefix . '#' . mt_rand(1, 9999), true));
    }

    public static function generateNum()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $uuid   = substr($charid, 0, 8) . substr($charid, 8, 4) . substr($charid, 12, 4) . substr($charid, 16, 4) . substr($charid, 20, 12);
        return $uuid;
    }
}
