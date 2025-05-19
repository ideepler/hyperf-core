<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\Logger;

use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

class AppendRequestIdProcessor implements ProcessorInterface
{

    use LogTrait;

	public function __invoke(array|LogRecord $record)
	{
        // 获取 trace 参数
        $traceParams = $this->getTraceParams();

        // 将追踪信息添加到日志上下文中
		$record['extra']['trace_id'] = $traceParams['traceId'];
		$record['extra']['span_id'] = $traceParams['spanId'];
		$record['extra']['parent_span_id'] = $traceParams['parentSpanId'];
		$record['extra']['coroutine_id'] = Coroutine::id();
		return $record;
	}
}
