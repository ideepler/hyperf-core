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
namespace Ideepler\HyperfCore\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Context\Context;
use Psr\Http\Message\ResponseInterface;
use Throwable;
use Ideepler\HyperfCore\Exception\BusinessException;
use Ideepler\HyperfCore\Exception\InvalidArgumentException;

class AppExceptionHandler extends ExceptionHandler
{
    public function __construct(protected StdoutLoggerInterface $logger)
    {
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {

		// 记录日志
	    $this->writeLogger($throwable);

		// 判断是否需以Json格式返回给客户端
		if($this->isWhiteException($throwable)){

			// 阻止异常冒泡
			$this->stopPropagation();

			$msgData = [
				'errno' => $throwable->getCode(),
				'msg' => $throwable->getMessage(),
				'data' => [],
				'time' => time()
			];

			if(Context::has('exception_data')){
				$msgData['data'] = Context::get('exception_data');
			}else{
				unset($msgData['data']);
			}

			// 空数组转化对象
			if(isset($msgData['data']) && empty($msgData['data'])){
				$msgData['data'] = (object)$msgData['data'];
			}

			return $response->withHeader(
				'Content-Type',
				'application/json; charset=utf-8'
			)->withBody(
				new SwooleStream(
					json_encode($msgData)
				)
			);

		}

		// 其余异常简单包装后继续抛出
        return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
    }

	/**
	 *  根据异常类型记录日志不同级别日志
	 * @param Throwable $throwable
	 */
	public function writeLogger(Throwable $throwable)
	{
		if($throwable instanceof InvalidArgumentException){
			$this->logger->info(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
		}elseif ($throwable instanceof BusinessException){
			$this->logger->warning(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
		}else{
			$this->logger->error(sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()));
			$this->logger->error($throwable->getTraceAsString());
		}
	}

	/**
	 *  白名单异常，无需抛出，直接返回给客户端.
	 * @param Throwable $throwable
	 * @return boolean
	 */
	public function isWhiteException(Throwable $throwable)
	{
		// 业务异常和参数异常直接输出json
		if($throwable instanceof InvalidArgumentException){
			return true;
		}elseif ($throwable instanceof BusinessException){
			return true;
		}

		// 错误编码为约定异常则直接输出
		if(strlen((string)$throwable->getCode()) === 5){
			return true;
		}

		return false;
	}

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
