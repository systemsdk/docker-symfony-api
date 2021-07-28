<?php

declare(strict_types=1);

namespace App\Utils\Traits;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Trait StopwatchAwareTrait
 *
 * NOTE: Do not use this in your services, just inject `Stopwatch` to service where you need it.
 *       This trait is just for quick debug purposes and nothing else.
 *
 * @package App\Utils\Traits
 */
trait StopwatchAwareTrait
{
    protected ?Stopwatch $stopwatch;

    /**
     * @see https://symfony.com/doc/current/service_container/autowiring.html#autowiring-other-methods-e-g-setters
     *
     * @required
     */
    public function setStopwatch(Stopwatch $stopwatch): self
    {
        $this->stopwatch = $stopwatch;

        return $this;
    }
}
