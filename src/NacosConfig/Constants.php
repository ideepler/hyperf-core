<?php

declare(strict_types=1);

namespace Ideepler\HyperfCore\NacosConfig;

class Constants extends Hyperf\ConfigNacos\Constants
{
    /**
     * 扩展一个合并类型，仅使用 array_merge 对数组顶层进行合并，用在需要用配置中心的键值覆盖仓库文件中的配置
     */
    public const CONFIG_MERGE_TOPLEVEL = 30;
}
