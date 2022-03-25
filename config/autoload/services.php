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
return [
    // 此处省略了其它同层级的配置
    'consumers' => [
        [
            'name' => 'FooService',
            'service' => App\RPC\FooInterface::class,
            'id' => App\RPC\FooInterface::class,
            'protocol' => Hyperf\RpcMultiplex\Constant::PROTOCOL_DEFAULT,
            'load_balancer' => 'random',
            'nodes' => [
                ['host' => '127.0.0.1', 'port' => 9501],
            ],
            // 配置项，会影响到 Packer 和 Transporter
            'options' => [
                'connect_timeout' => 5.0,
                'recv_timeout' => 5.0,
                'settings' => [
                    'open_length_check' => true,
                    'package_length_type' => 'N',
                    'package_length_offset' => 0,
                    'package_body_offset' => 4,
                ],
                // 重试次数，默认值为 2，收包超时不进行重试。暂只支持 JsonRpcPoolTransporter
                'retry_count' => 2,
                // 重试间隔，毫秒
                'retry_interval' => 100,
                // 心跳间隔
                'heartbeat' => 1,
            ],
        ],
    ],
];
