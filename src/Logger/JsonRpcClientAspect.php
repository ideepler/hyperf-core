<?php

namespace Ideepler\HyperfCore\Logger;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Context\Context;
use Hyperf\RpcClient\Client;

#[Aspect]
class JsonRpcClientAspect extends AbstractAspect
{

    use LogTrait;

    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [
        Client::class . '::send',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {

        // 获取原参数
        $data = $proceedingJoinPoint->arguments['keys']['data'] ?? [];

        if (is_array($data['context'])) {

            // 获取 trace 参数
            $traceParams = $this->getTraceParams();

            // 获取 context 参数并追加
            $data['context'] = array_merge($data['context'], [
                'traceId' => $traceParams['traceId'],
                'parentSpanId' => $traceParams['spanId'],
            ]);

            // 重新传递参数
            $proceedingJoinPoint->arguments['keys']['data'] = $data;
        }

        return $proceedingJoinPoint->process();

    }
}
