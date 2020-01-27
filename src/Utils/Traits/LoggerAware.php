<?php
declare(strict_types = 1);
/**
 * /src/Utils/Traits/LoggerAware.php
 */

namespace App\Utils\Traits;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerAware
 *
 * NOTE: Do not use this in your services, just inject `LoggerInterface` to service where you need it.
 *       This trait is just for quick debug purposes and nothing else.
 *
 * @package App\Utils\Traits
 */
trait LoggerAware
{
    protected LoggerInterface $logger;

    /**
     * @see https://symfony.com/doc/current/service_container/autowiring.html#autowiring-other-methods-e-g-setters
     *
     * @required
     *
     * @param LoggerInterface $logger
     *
     * @return self
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
