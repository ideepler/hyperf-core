<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\Logger;

use Psr\Container\ContainerInterface;

class StdoutLoggerFactory
{
	public function __invoke(ContainerInterface $container)
	{
		return Log::get('sys');
	}
}