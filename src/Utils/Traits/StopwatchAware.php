<?php
declare(strict_types = 1);
/**
 * /src/Utils/Traits/StopwatchAware.php
 */

namespace App\Utils\Traits;

use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Trait StopwatchAware
 *
 * @package App\Utils\Traits
 */
trait StopwatchAware
{
    protected Stopwatch $stopwatch;

    /**
     * @see https://symfony.com/doc/current/service_container/autowiring.html#autowiring-other-methods-e-g-setters
     *
     * @required
     *
     * @param Stopwatch $stopwatch
     *
     * @return self
     */
    public function setStopwatch(Stopwatch $stopwatch): self
    {
        $this->stopwatch = $stopwatch;

        return $this;
    }
}
