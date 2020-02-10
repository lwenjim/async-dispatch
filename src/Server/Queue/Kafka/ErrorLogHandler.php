<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 4/18/2019
 * Time: 1:56 PM
 */

namespace AsyncDis\Server\Queue\Kafka;

class ErrorLogHandler extends \Monolog\Handler\ErrorLogHandler
{
    protected function write(array $record)
    {
        if ($this->expandNewlines) {
            $lines = preg_split('{[\r\n]+}', (string)$record['formatted']);
            foreach ($lines as $line) {
                debug($line);
            }
        } else {
			debug((string)$record['formatted']);
        }
    }
}
