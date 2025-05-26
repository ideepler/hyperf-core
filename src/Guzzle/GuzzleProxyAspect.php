<?php

namespace Ideepler\HyperfCore\Guzzle;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\Exception\InvalidConfigException;
use GuzzleHttp\Client;

use function Hyperf\Config\config;

#[Aspect]
class GuzzleProxyAspect extends AbstractAspect
{

    #[Inject]
    protected StdoutLoggerInterface $logger;

    // 要切入的类或 Trait，可以多个，亦可通过 :: 标识到具体的某个方法，通过 * 可以模糊匹配
    public array $classes = [
        Client::class . '::__construct',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {

        // 获取原参数
        $arguments = $proceedingJoinPoint->getArguments();

        // 判断需要追加代理的场景
        if(isset($arguments[0]['proxy']) && $arguments[0]['proxy'] === true){
            $socks5Proxy = config('proxy.socks5proxy');

            if(empty($socks5Proxy)){
                throw new InvalidConfigException(sprintf('proxy config[%s] is not defined.', 'socks5proxy'));
            }

            $arguments[0]['proxy'] = [
                'http' => $socks5Proxy,
                'https' => $socks5Proxy
            ];

            $this->logger->debug('guzzle construct aop', ['args' => $arguments]);
        }

        $proceedingJoinPoint->process();

    }
}
