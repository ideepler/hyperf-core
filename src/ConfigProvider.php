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

use Ideepler\HyperfCore\NacosServiceGovernance\InstanceOffline;
use Ideepler\HyperfCore\NacosServiceGovernance\InstanceUnregister;
use Ideepler\HyperfCore\NacosServiceGovernance\OnShutdownUnregisterListener;

use function Hyperf\Support\env;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'commands' => [
                InstanceUnregister::class,
                InstanceOffline::class,
            ],
            'listeners' => [
                OnShutdownUnregisterListener::class,
            ],
	        'publish' => [
                [
                    'id' => 'config-logger',
                    'description' => 'logger config.', // 日志配置文件
                    'source' => __DIR__ . '/../publish/logger.php',  // 原配置文件路径
                    'destination' => BASE_PATH . '/config/routes/logger.php', // 复制为这个路径下的文件名
                ],
                [
                    'id' => 'config-exceptions',
                    'description' => 'exceptions config.', // 异常配置文件
                    'source' => __DIR__ . '/../publish/exceptions.php',  // 原配置文件路径
                    'destination' => BASE_PATH . '/config/autoload/exceptions.php', // 复制为这个路径下的文件名
                ],
                [
                    'id' => 'config-proxy',
                    'description' => 'proxy config.', // 代理配置文件
                    'source' => __DIR__ . '/../publish/proxy.php',  // 原配置文件路径
                    'destination' => BASE_PATH . '/config/autoload/proxy.php', // 复制为这个路径下的文件名
                ]
	        ],
        ];
    }
}
