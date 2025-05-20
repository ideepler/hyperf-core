# Installation using Composer

```
composer require ideepler/hyperf-core
```

# Publish config

```
php bin/hyperf.php vendor:publish ideepler/hyperf-core
```

# Nacos config

```
'driver' => Ideepler\HyperfCore\NacosConfig\NacosDriver::class,
'merge_mode' => Ideepler\HyperfCore\NacosConfig\Constants::CONFIG_MERGE_OVERWRITE,
```

# Related Modules

 - utils
 - exception
 - logger
 - nacos config
 - nacos unregister