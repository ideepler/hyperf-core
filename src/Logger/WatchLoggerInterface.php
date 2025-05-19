<?php

declare(strict_types=1);
/**
 * watch logger interface.
 */

namespace Ideepler\HyperfCore\Logger;

use Psr\Log\LoggerInterface;

interface WatchLoggerInterface extends LoggerInterface
{
    public function watch(string|\Stringable $message, array $context = []): void;
}
