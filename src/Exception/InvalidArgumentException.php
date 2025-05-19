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

use Hyperf\Server\Exception\ServerException;
use Throwable;

class InvalidArgumentException extends ServerException
{
    public function __construct(int $code = 0, $message = null, Throwable $previous = null)
    {

	    // 错误编码code必填校验
	    if (empty($code)) {
		    throw new \Exception('BusinessException code must set.');
	    }

	    // 对长度5位数的异常做特殊处理
	    if(strlen((string)$code) === 5 && !is_string($message)){
		    if(is_null($message)){
			    $message = [];
		    }
		    $message = get_msg_by_code($code, $message);
	    }

	    // 参数校验异常msg不能为空
	    if (empty($message)) {
		    throw new \Exception('InvalidArgumentException msg cannot be empty.');
	    }

        parent::__construct($message, $code, $previous);
    }
}
