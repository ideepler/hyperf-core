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

use Hyperf\Validation\ValidationException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
	public function handle(Throwable $throwable, ResponseInterface $response)
	{
		$this->stopPropagation();
		/** @var \Hyperf\Validation\ValidationException $throwable */
		$body = $throwable->validator->errors()->first();
		$msgData = [
			'errno' => 10016,
			'msg' => $body,
			'time' => time()
		];
		if (! $response->hasHeader('content-type')) {
			$response = $response->withAddedHeader('Content-Type', 'application/json; charset=utf-8');
		}
		return $response->withStatus(200)->withBody(new SwooleStream(json_encode($msgData)));
	}

	public function isValid(Throwable $throwable): bool
	{
		return $throwable instanceof ValidationException;
	}
}
