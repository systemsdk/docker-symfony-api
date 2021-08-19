<?php

declare(strict_types=1);

namespace App\Utils\Traits;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Trait LoggerAwareTrait
 *
 * NOTE: Do not use this in your services, just inject `LoggerInterface` to service where you need it.
 *       This trait is just for quick debug purposes and nothing else.
 *
 * @package App\Utils\Traits
 */
trait LoggerAwareTrait
{
    protected ?LoggerInterface $logger;

    #[Required]
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
