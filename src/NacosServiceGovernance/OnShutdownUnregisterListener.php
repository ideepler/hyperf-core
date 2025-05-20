<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\NacosServiceGovernance;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\OnShutdown;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Hyperf\Contract\StdoutLoggerInterface;

/**
 * OnShutdownListener.
 */
class OnShutdownUnregisterListener implements ListenerInterface
{

    /**
     * @return string[]
     */
    public function listen(): array
    {
        return [
            OnShutdown::class,
        ];
    }
    /**
     * @param object $event 事件.
     * @return void
     */
    public function process(object $event): void
    {

        $params = ["command" => 'service-governance:instance-unregister'];

        $input = new ArrayInput($params);
        $output = new NullOutput();

        $container = \Hyperf\Context\ApplicationContext::getContainer();
        $application = $container->get(\Hyperf\Contract\ApplicationInterface::class);
        $application->setAutoExit(false);
        $exitCode = $application->run($input, $output);

        $container->get(StdoutLoggerInterface::class)->info('shutdown', ['time' => time()]);

    }
}
