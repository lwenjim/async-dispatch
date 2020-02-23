<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/16/2019
 * Time: 3:42 PM
 */

namespace AsyncDispatch;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

class Log
{
    protected        $log      = null;
    protected        $rate     = 0;
    protected        $handler  = null;
    protected        $ini      = null;
    protected static $instance = null;

    public static function getInstance(): self
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function __construct()
    {
        $this->ini = Config::ini('log');
        $this->log = (new Logger($this->ini->get('channel')))->pushHandler($this->getHandler());
        $this->clear();
    }

    protected function getHandler()
    {
        if (!empty($this->handler)) {
            return $this->handler;
        }
        $this->handler = new class ($this->getPath(), Logger::DEBUG, true, 0777) extends \Monolog\Handler\StreamHandler
        {
            protected function write(array $record)
            {
                [, $theDate] = explode('-', pathinfo($this->getUrl(), PATHINFO_FILENAME), 2);
                if ($theDate != ($currentDate = date('Y-m-d'))) {
                    unset($this->stream);
                    $this->url = str_replace($theDate, $currentDate, $this->getUrl());
                }
                parent::write($record);
            }
        };
        $this->handler->setFormatter(new LineFormatter($this->getOutputTemplate(), "Y-m-d H:i:s"));
        return $this->handler;
    }

    protected function getPath()
    {
        return Config::getAppPath(). $this->ini->get('path') . '/' . $this->ini->get('channel') . '-' . date('Y-m-d') . '.log';
    }

    protected function getOutputTemplate()
    {
        return "[%datetime%] ## %channel%.%level_name% ## " . uniqid() . " ## %message% ## %context% ## %extra%\n";
    }

    protected function clear()
    {
        if (Config::getEnv() !== Config::ENV_DEV) return;
        foreach (glob($this->ini->get('path') . "/*.log") as $value) {
            if (!file_exists($value)) continue;
            if (date('Y-m-d') == date('Y-m-d', filemtime($value))) continue;
            unlink($value);
        }
    }

    public function debug($str, $context = [])
    {
        $this->log->debug($str, $context);
    }

    public function error($str, $context = [])
    {
        $this->log->error($str, $context);
        return $this;
    }
}
