<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Resource;

use App\ApiKey\Application\Resource\Interfaces\ApiKeyDeleteResourceInterface;
use App\ApiKey\Domain\Entity\ApiKey as Entity;
use App\ApiKey\Domain\Repository\Interfaces\ApiKeyRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceDeleteMethod;

/**
 * @package App\ApiKey
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity getReference(string $id, ?string $entityManagerName = null)
 * @method \App\ApiKey\Infrastructure\Repository\ApiKeyRepository getRepository()
 * @method Entity delete(string $id, ?bool $flush = null, ?string $entityManagerName = null)
 *
 * @codingStandardsIgnoreEnd
 */
class ApiKeyDeleteResource extends RestSmallResource implements ApiKeyDeleteResourceInterface
{
    use ResourceDeleteMethod;

    /**
     * @param \App\ApiKey\Infrastructure\Repository\ApiKeyRepository $repository
     */
    public function __construct(
        RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
