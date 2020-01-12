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
