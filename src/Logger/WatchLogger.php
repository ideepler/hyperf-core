<?php

declare(strict_types=1);
/**
 * watch logger.
 */

namespace Ideepler\HyperfCore\Logger;

use Hyperf\Logger\Logger;

class WatchLogger extends Logger implements WatchLoggerInterface
{

    public function __construct(string $name = 'watch', array $handlers = [], array $processors = [])
    {
        parent::__construct($name, $handlers, $processors);

    }

    /**
     * 新增 debug 方法，使其记录 info 级别日志
     * @param string|\Stringable $message
     * @param array $context
     * @return void
     */
    public function watch(string|\Stringable $message, array $context = []): void
    {
        $this->addRecord(Logger::ALERT, $message, $context);
    }

}
