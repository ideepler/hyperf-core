<?php

/**
 * 实例注销-将实例从服务列表里面删除.
 */

declare(strict_types=1);

namespace Ideepler\HyperfCore\NacosServiceGovernance;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\RpcServer\Annotation\RpcService;
use Hyperf\ServiceGovernanceNacos\Client;
use Hyperf\Support\Network;
use Symfony\Component\Console\Input\InputOption;

use function Hyperf\Config\config;

/**
 * 实例注销.
 */
class InstanceUnregister extends \Hyperf\Command\Command
{
    protected ?string $name = 'service-governance:instance-unregister';
    protected string $description = '实例注销-实例从服务列表里面删除';

    /**
     * 逻辑.
     * @return void
     */
    public function handle()
    {
        if (
            !config('services.drivers.nacos.auto_unregister', false)
            && !$this->input->getOption('unregister')
        ) {
            // 没有开启自动下线和要求强制下线的话就不操作.
            return;
        }
        $client = \Hyperf\Context\ApplicationContext::getContainer()->get(Client::class);
        $classs = (AnnotationCollector::getClassesByAnnotation(RpcService::class));
        $server = $this->getService();
        $ip = Network::ip();
        foreach ($classs as $rpcService) {
            if (!isset($rpcService->publishTo, $rpcService->server, $server[$rpcService->server])) {
                continue;
            }
            for ($retry = 0; $retry < 3; $retry++) {
                $up = $client->instance->delete(
                    $rpcService->name,
                    config('services.drivers.nacos.group_name'),
                    $ip,
                    $server[$rpcService->server]['port'],
                    [
                        'namespaceId' => config('services.drivers.nacos.namespace_id', ''),
                    ]
                )->getStatusCode();
                if ($up == 200) {
                    $this->info($rpcService->name . '已注销');
                    break;
                }
            }
        }
    }

    /**
     * 配置参数.
     * @return void
     */
    public function configure()
    {
        parent::configure();
        $this->addOption('unregister', null, InputOption::VALUE_OPTIONAL, '0不处理,1下线', 0);
    }

    /**
     * 获取服务.
     * @return array
     */
    public function getService()
    {
        return array_column(config('server.servers', []), null, 'name');
    }
}
