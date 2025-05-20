<?php

namespace Ideepler\HyperfCore\Logger;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;
use Hyperf\Logger\Exception\InvalidConfigException;
use Hyperf\Logger\Logger;

use function Hyperf\Support\make;

#[Aspect]
class LoggerFactoryAspect extends AbstractAspect
{

    use LogTrait;

    #[Inject]
    protected ConfigInterface $config;

    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [
        LoggerFactory::class . '::make',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint): LoggerInterface
    {

        // 获取原参数
        $args = $proceedingJoinPoint->getArguments();
        $name = $args[0];
        $group = $args[1];

        $config = $this->config->get('logger');
        if (! isset($config[$group])) {
            throw new InvalidConfigException(sprintf('Logger config[%s] is not defined.', $group));
        }

        $config = $config[$group];

        // 获取目标类实例，即 LoggerFactory
        $loggerFactory = $proceedingJoinPoint->getInstance();

        // 使用反射来访问 `handlers` 方法
        $reflectionMethodHandlers = new \ReflectionMethod(LoggerFactory::class, 'handlers');
        $reflectionMethodHandlers->setAccessible(true);  // 设置为可访问
        $handlers = $reflectionMethodHandlers->invoke($loggerFactory, $config);  // 调用 `handlers` 方法

        // 使用反射来访问 `processors` 方法
        $reflectionMethodProcessors = new \ReflectionMethod(LoggerFactory::class, 'processors');
        $reflectionMethodProcessors->setAccessible(true);  // 设置为可访问
        $processors = $reflectionMethodProcessors->invoke($loggerFactory, $config);  // 调用 `processors` 方法

        return make(WatchLogger::class, [
            'name' => $name,
            'handlers' => $handlers,
            'processors' => $processors,
        ]);

    }
}
