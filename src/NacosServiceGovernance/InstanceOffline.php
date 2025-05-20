<?php

/**
 * 实例下线-将实例标记下线.
 */

declare(strict_types=1);

namespace Ideepler\HyperfCore\NacosServiceGovernance;

use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\RpcServer\Annotation\RpcService;
use Hyperf\ServiceGovernanceNacos\Client;
use Hyperf\ServiceGovernance\ServiceManager;
use Hyperf\Support\Network;
use Symfony\Component\Console\Input\InputOption;

use function Hyperf\Config\config;

/**
 * 实例下线.
 */
class InstanceOffline extends \Hyperf\Command\Command
{
    protected ?string $name = 'service-governance:instance-offline';
    protected string $description = '实例下线-标记实例下线';

    /**
     * 逻辑.
     * @return void
     */
    public function handle()
    {
        if (
            !config('services.drivers.nacos.auto_offline', false)
            && !$this->input->getOption('offline')
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
                $up = $client->instance->update($ip, $server[$rpcService->server]['port'], $rpcService->name, [
                    'enabled' => false,
                    'groupName' => config('services.drivers.nacos.group_name'),
                    'namespaceId' => config('services.drivers.nacos.namespace_id', ''),
                ])->getStatusCode();
                if ($up == 200) {
                    $this->info($rpcService->name . '已下线');
                    break;
                }
            }
        }
        $sleep = intval($this->input->getOption('sleep'));
        if ($sleep > 0) {
            sleep($sleep);
        }
    }

    /**
     * 配置参数.
     * @return void
     */
    public function configure()
    {
        parent::configure();
        $this->addOption('sleep', null, InputOption::VALUE_OPTIONAL, '处理完过后sleep日期', 0);
        $this->addOption('offline', null, InputOption::VALUE_OPTIONAL, '0不处理,1下线', 0);
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
