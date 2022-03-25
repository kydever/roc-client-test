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
namespace App\RPC;

interface FooInterface
{
    /**
     * @return array ['is_success' => true]
     */
    public function save(int $id, array $input): array;
}
