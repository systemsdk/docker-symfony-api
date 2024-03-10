<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Resource;

use App\ApiKey\Application\Resource\Interfaces\ApiKeyFindOneResourceInterface;
use App\ApiKey\Domain\Entity\ApiKey as Entity;
use App\ApiKey\Domain\Repository\Interfaces\ApiKeyRepositoryInterface as RepositoryInterface;
use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceFindOneByMethod;
use App\General\Application\Rest\Traits\Methods\ResourceFindOneMethod;

/**
 * @package App\ApiKey
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity getReference(string $id, ?string $entityManagerName = null)
 * @method \App\ApiKey\Infrastructure\Repository\ApiKeyRepository getRepository()
 * @method Entity|null findOne(string $id, ?bool $throwExceptionIfNotFound = null, ?string $entityManagerName = null)
 * @method Entity|null findOneBy(array $criteria, ?array $orderBy = null, ?bool $throwExceptionIfNotFound = null, ?string $entityManagerName = null)
 *
 * @codingStandardsIgnoreEnd
 */
class ApiKeyFindOneResource extends RestSmallResource implements ApiKeyFindOneResourceInterface
{
    use ResourceFindOneMethod;
    use ResourceFindOneByMethod;

    /**
     * @param \App\ApiKey\Infrastructure\Repository\ApiKeyRepository $repository
     */
    public function __construct(
        RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
