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
namespace Ideepler\HyperfCore\Exception;

use Hyperf\Server\Event;
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
			        'id' => 'config-exceptions',
			        'description' => 'rcp debug routes.', // 描述
			        // 建议默认配置放在 publish 文件夹中，文件命名和组件名称相同
			        'source' => __DIR__ . '/../publish/exceptions.php',  // 对应的配置文件路径
			        'destination' => BASE_PATH . '/config/autoload/exceptions.php', // 复制为这个路径下的该文件
		        ],
	        ],
        ];
    }
}
