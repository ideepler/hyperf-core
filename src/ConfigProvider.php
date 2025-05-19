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
namespace Ideepler\HyperfCore;

use function Hyperf\Support\env;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
	        'publish' => [
                [
                    'id' => 'config-logger',
                    'description' => 'logger config.', // 日志配置文件
                    'source' => __DIR__ . '/../publish/logger.php',  // 对应的配置文件路径
                    'destination' => BASE_PATH . '/config/routes/logger.php', // 复制为这个路径下的文件名
                ],
                [
                    'id' => 'config-exceptions',
                    'description' => 'exceptions config.', // 异常配置文件
                    'source' => __DIR__ . '/../publish/exceptions.php',  // 对应的配置文件路径
                    'destination' => BASE_PATH . '/config/routes/exceptions.php', // 复制为这个路径下的文件名
                ]
	        ],
        ];
    }
}
