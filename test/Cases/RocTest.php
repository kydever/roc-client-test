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
namespace HyperfTest\Cases;

use App\RPC\FooInterface;
use Hyperf\Engine\Channel;
use Hyperf\RpcClient\Client;
use Hyperf\RpcClient\ServiceClient;
use Hyperf\RpcMultiplex\Socket;
use Hyperf\RpcMultiplex\SocketFactory;
use Hyperf\RpcMultiplex\Transporter;
use Hyperf\Utils\Codec\Json;
use Hyperf\Utils\Reflection\ClassInvoker;
use HyperfTest\HttpTestCase;
use Multiplex\ChannelManager;
use Multiplex\Contract\HasHeartbeatInterface;
use Multiplex\Contract\PackerInterface;
use Multiplex\Packet;

/**
 * @internal
 * @coversNothing
 */
class RocTest extends HttpTestCase
{
    public function testRocRequest()
    {
        $res = di()->get(FooInterface::class)->save(1, ['name' => '李铭昕', 'gender' => 1]);

        $this->assertIsArray($res);
        $this->assertTrue($res['is_success']);
    }

    public function testSendTwoPacketAtTheSameTime()
    {
        $interface = new ClassInvoker(di()->get(FooInterface::class));
        $client = $interface->client;
        $this->assertInstanceOf(ServiceClient::class, $client);

        $client = (new ClassInvoker($client))->client;
        $this->assertInstanceOf(Client::class, $client);

        $transporter = (new ClassInvoker($client))->transporter;
        $this->assertInstanceOf(Transporter::class, $transporter);

        $factory = (new ClassInvoker($transporter))->factory;
        $this->assertInstanceOf(SocketFactory::class, $factory);

        /** @var Socket $socket */
        $socket = new ClassInvoker($factory->get());
        $socket->loop();

        /** @var PackerInterface $packer */
        $packer = $socket->packer;
        /** @var ChannelManager $channelManager */
        $channelManager = $socket->channelManager;

        /** @var Channel $chan */
        $chan = $socket->chan;
        $channelManager->get(1, true);
        $channelManager->get(2, true);
        $payload = $packer->pack(
            new Packet(1, '{"id":"624d59901bd82","path":"\/foo\/save","data":[1,{"name":"李铭昕","gender":1}],"context":[]}')
        );
        $payload .= $packer->pack(
            new Packet(2, '{"id":"624d59901bd83","path":"\/foo\/save","data":[1,{"name":"李铭昕","gender":1}],"context":[]}')
        );
        $chan->push($payload);
        $res = $channelManager->get(1)->pop(5);
        $data = Json::decode($res);
        $this->assertSame('624d59901bd82', $data['id']);
        $res = $channelManager->get(2)->pop(5);
        $data = Json::decode($res);
        $this->assertSame('624d59901bd83', $data['id']);
    }

    public function testSendOnePacketAtMultiTimes()
    {
        $interface = new ClassInvoker(di()->get(FooInterface::class));
        $client = $interface->client;
        $this->assertInstanceOf(ServiceClient::class, $client);

        $client = (new ClassInvoker($client))->client;
        $this->assertInstanceOf(Client::class, $client);

        $transporter = (new ClassInvoker($client))->transporter;
        $this->assertInstanceOf(Transporter::class, $transporter);

        $factory = (new ClassInvoker($transporter))->factory;
        $this->assertInstanceOf(SocketFactory::class, $factory);

        /** @var Socket $socket */
        $socket = new ClassInvoker($factory->get());
        $socket->loop();

        /** @var PackerInterface $packer */
        $packer = $socket->packer;
        /** @var ChannelManager $channelManager */
        $channelManager = $socket->channelManager;

        /** @var Channel $chan */
        $chan = $socket->chan;
        $channelManager->get(1, true);
        $payload = $packer->pack(
            new Packet(1, $body = '{"id":"624d59901bd82","path":"\/foo\/save","data":[1,{"name":"李铭昕","gender":1}],"context":[]}')
        );
        $payloads = str_split($payload, intval(strlen($body) / 2 + 1));
        foreach ($payloads as $payload){
            usleep(100000);
            $chan->push($payload);
        }
        $res = $channelManager->get(1)->pop(5);
        $data = Json::decode($res);
        $this->assertSame('624d59901bd82', $data['id']);
    }
}
