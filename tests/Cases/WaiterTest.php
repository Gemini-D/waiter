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

use Gemini\Waiter\Exception\WaitTimeoutException;
use Hyperf\Utils\Coroutine;

/**
 * @internal
 * @coversNothing
 */
class WaiterTest extends AbstractTestCase
{
    public function testWait()
    {
        $this->runInCoroutine(function () {
            $id = uniqid();
            $result = wait(function () use ($id) {
                return $id;
            });

            $this->assertSame($id, $result);

            $id = rand(0, 9999);
            $result = wait(function () use ($id) {
                return $id + 1;
            });

            $this->assertSame($id + 1, $result);
        });
    }

    public function testWaitNone()
    {
        $this->runInCoroutine(function () {
            $callback = function () {
            };
            $result = wait($callback);
            $this->assertSame($result, $callback());
            $this->assertSame(null, $result);

            $callback = function () {
                return null;
            };
            $result = wait($callback);
            $this->assertSame($result, $callback());
            $this->assertSame(null, $result);
        });
    }

    public function testWaitException()
    {
        $this->runInCoroutine(function () {
            $message = uniqid();
            $callback = function () use ($message) {
                throw new \RuntimeException($message);
            };

            try {
                wait($callback);
            } catch (\Throwable $exception) {
                $this->assertInstanceOf(\RuntimeException::class, $exception);
                $this->assertSame($message, $exception->getMessage());
            }
        });
    }

    public function testWaitReturnException()
    {
        $this->runInCoroutine(function () {
            $message = uniqid();
            $callback = function () use ($message) {
                return new \RuntimeException($message);
            };

            $result = wait($callback);
            $this->assertInstanceOf(\RuntimeException::class, $result);
            $this->assertSame($message, $result->getMessage());
        });
    }

    public function testTimeout()
    {
        $this->runInCoroutine(function () {
            $callback = function () {
                Coroutine::sleep(0.5);
                return true;
            };

            try {
                wait($callback, 0.001);
                $this->assertTrue(false);
            } catch (\Throwable $exception) {
                $this->assertInstanceOf(WaitTimeoutException::class, $exception);
                $this->assertSame('Channel wait failed, reason: Timed out for 0.001 s', $exception->getMessage());
            }
        });
    }
}
