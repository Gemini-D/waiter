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
use Gemini\Waiter\Waiter;
use Hyperf\Utils\ApplicationContext;

if (! function_exists('wait')) {
    function wait(Closure $closure, ?float $timeout = null)
    {
        if (ApplicationContext::hasContainer()) {
            $waiter = ApplicationContext::getContainer()->get(Waiter::class);
            return $waiter->wait($closure, $timeout);
        }
        return (new Waiter())->wait($closure, $timeout);
    }
}
