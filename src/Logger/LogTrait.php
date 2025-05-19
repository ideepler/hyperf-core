<?php

namespace Ideepler\HyperfCore\Logger;

use Hyperf\Context\Context;

/**
 * log 设置 trace 参数公共代码
 */
trait LogTrait {

    /**
     * @param string $logPrefix 日志ID前缀，在必要时可传递
     * @return array
     */
    final function getTraceParams(string $logPrefix = ''): array {

        // 从 Context 中获取 traceId、spanId 和 parentSpanId
        $traceId = Context::get('traceId', genUniqueId($logPrefix));
        $spanId = Context::get('spanId', genUniqueId($logPrefix));
        $parentSpanId = Context::get('parentSpanId', '');

        // 将参数写入 Context
        if(!Context::has('traceId')){
            Context::set('traceId', $traceId);
        }
        if(!Context::has('spanId')){
            Context::set('spanId', $spanId);
        }
        if(!Context::has('parentSpanId')){
            Context::set('parentSpanId', $parentSpanId);
        }

        return [
            'traceId' => $traceId,
            'spanId' => $spanId,
            'parentSpanId' => $parentSpanId,
        ];

    }

}