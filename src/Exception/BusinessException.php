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
use Hyperf\Context\Context;
use Throwable;

/**
 * 此异常主要是为了抛出errno的Json
 * 在使用默认msg的情况下，仅需要输入code，故code顺序在前
 * 使用此异常code必须明确指定，消息可支持字符串或数组
 * 若需要抛出非json响应的异常，可以抛出Exception
 * @example
 * throw New BusinessException(500, 'aaa', ['uid' => '1001029932']);
 * throw New BusinessException(10016, ['uid'], ['uid' => '1001029932']);
 */
class BusinessException extends ServerException
{
    public function __construct(int $code = 0, $message = null, array $data = [], Throwable $previous = null)
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
			// 主动获取msg对应的配置
		     throw new \Exception('BusinessException msg cannot be empty.');
	    }

	    // 当是数组形式参数时，对参数进行存储，以备在异常处理类AppExceptionHandler中获取使用
	    if(is_array($message)){
		    Context::set('exception_message', $data);
	    }

		// 需要存储抛出异常是的data数据
		if(!empty($data) && is_array($data)){
			Context::set('exception_data', $data);
		}

        parent::__construct($message, $code, $previous);
    }
}
