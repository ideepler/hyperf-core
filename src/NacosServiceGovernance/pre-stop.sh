#!/bin/bash
#服务下线
php bin/hyperf.php service-governance:instance-offline
#服务下线后等10秒钟
sleep 10