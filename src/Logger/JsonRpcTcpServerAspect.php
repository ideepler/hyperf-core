<?php

namespace Ideepler\HyperfCore\Logger;

use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Context\Context;
use Hyperf\JsonRpc\TcpServer;

#[Aspect]
class JsonRpcTcpServerAspect extends AbstractAspect
{

    #[Inject]
    protected ContainerInterface $container;

    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [
        TcpServer::class . '::buildJsonRpcRequest',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {

        // 获取原参数
        $data = $proceedingJoinPoint->arguments['keys']['data'] ?? [];

        // 追加附加逻辑
        $traceId = $data['context']['traceId'];
        $parentSpanId = $data['context']['parentSpanId'];

        // 将附加参数写入 Context
        if(!Context::has('traceId')){
            Context::set('traceId', $traceId);
        }
        if(!Context::has('spanId')){
            Context::set('spanId', genUniqueId());
        }
        if(!Context::has('parentSpanId')){
            Context::set('parentSpanId', $parentSpanId);
        }

        // 执行原逻辑
        return $proceedingJoinPoint->process();

    }
}
