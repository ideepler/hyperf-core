<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Ideepler\HyperfCore\Logger\AppendRequestIdProcessor;

use function Hyperf\Support\env;

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => env('APP_LOGGER_PATH', BASE_PATH . '/runtime') . '/logs/hyperf.log',
                'level' => intval(env('APP_LOGGER_LEVEL', Monolog\Logger::INFO)),
            ],
        ],
        'formatter' => [
            'class' => \Monolog\Formatter\JsonFormatter::class,
            'constructor' => [],
        ],
        'processors' => [
            [
                'class' => AppendRequestIdProcessor::class,
            ],
        ],
    ],
];
