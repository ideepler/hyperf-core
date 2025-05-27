<?php

namespace Ideepler\HyperfCore\Logger;

use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Context\Context;
use Hyperf\Framework\Logger\StdoutLogger;

use function Hyperf\Support\env;

#[Aspect]
class StdoutLoggerAspect extends AbstractAspect
{

    #[Inject]
    protected ContainerInterface $container;

    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [
        StdoutLogger::class . '::log',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {

        // 获取原参数
        $context = $proceedingJoinPoint->arguments['keys']['context'] ?? [];

        // 当日志输出到终端时，对将内层数组转换为 json 字符串
        if(!env('DAEMONIZE') && !empty($context)){
            foreach ($context as $key => $value) {
                if (is_array($value)) {
                    $context[$key] = json_encode($value);
                }
            }
            $proceedingJoinPoint->arguments['keys']['context'] = $context;
        }

        // 执行原逻辑
        return $proceedingJoinPoint->process();

    }
}
