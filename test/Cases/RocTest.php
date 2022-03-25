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
use HyperfTest\HttpTestCase;

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
}
