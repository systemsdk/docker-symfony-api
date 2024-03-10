<?php

declare(strict_types=1);

namespace App\ApiKey\Application\Resource;

use App\ApiKey\Application\Resource\Interfaces\ApiKeyUpdateResourceInterface;
use App\ApiKey\Domain\Entity\ApiKey as Entity;
use App\ApiKey\Domain\Repository\Interfaces\ApiKeyRepositoryInterface as RepositoryInterface;
use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Rest\RestSmallResource;
use App\General\Application\Rest\Traits\Methods\ResourceSaveMethod;
use App\General\Application\Rest\Traits\Methods\ResourceUpdateMethod;
use App\General\Domain\Entity\Interfaces\EntityInterface;

/**
 * @package App\ApiKey
 *
 * @psalm-suppress LessSpecificImplementedReturnType
 * @codingStandardsIgnoreStart
 *
 * @method Entity getReference(string $id, ?string $entityManagerName = null)
 * @method \App\ApiKey\Infrastructure\Repository\ApiKeyRepository getRepository()
 * @method Entity update(string $id, RestDtoInterface $dto, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 * @method Entity save(EntityInterface $entity, ?bool $flush = null, ?bool $skipValidation = null, ?string $entityManagerName = null)
 *
 * @codingStandardsIgnoreEnd
 */
class ApiKeyUpdateResource extends RestSmallResource implements ApiKeyUpdateResourceInterface
{
    use ResourceUpdateMethod;
    use ResourceSaveMethod;

    /**
     * @param \App\ApiKey\Infrastructure\Repository\ApiKeyRepository $repository
     */
    public function __construct(
        RepositoryInterface $repository,
    ) {
        parent::__construct($repository);
    }
}
