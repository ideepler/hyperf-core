<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\Utils;

use Throwable;

/**
 * 异常处理类，以便在写入日志时记录的更详细
 */
class ExceptionUtil
{
    /**
     * 格式化异常
     *
     * @param Throwable $e     异常.
     * @param integer   $depth 深度.
     * @return array
     * @author haidong
     */
    public static function parseException(Throwable $e, int $depth = 10): array
    {
        $data = [
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => (int)$e->getCode(),
            'file' => $e->getFile() . ':' . $e->getLine(),
        ];

        $trace = $e->getTrace();
        if ($depth) {
            $trace = array_slice($trace, 0, $depth);
        }
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $data['trace'][] = sprintf("%s(%s): %s", $frame['file'], $frame['line'], $frame['function'] ?? "none");
            }
        }

        if ($previous = $e->getPrevious()) {
            $data['previous'] = self::parseException($previous, 1);
        }

        return $data;
    }

    /**
     * 获取调用堆栈
     * @param integer $limit 深度.
     * @return array
     * @author haidong
     */
    public static function debugTrace(int $limit = 20): array
    {
        $data = [];
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $limit);
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $func = $frame['function'] ?? "none";
                if (isset($frame['class'])) {
                    $func = sprintf("%s%s%s", $frame['class'], $frame['type'] ?? '-', $func);
                }
                $data[] = sprintf("%s(%s): %s", $frame['file'], $frame['line'], $func);
            }
        }

        return $data;
    }
}
