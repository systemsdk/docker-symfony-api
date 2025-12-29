<?php

declare(strict_types=1);

namespace App\General\Application\Rest\Traits;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Application\Exception\ValidatorException;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Repository\Interfaces\BaseRepositoryInterface;
use Override;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;
use UnexpectedValueException;

use function array_keys;
use function sprintf;

/**
 * @package App\General
 */
trait RestResourceBaseMethods
{
    private ValidatorInterface $validator;
    private string $dtoClass = '';

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getSerializerContext(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getRepository(): BaseRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function setValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getDtoClass(): string
    {
        if ($this->dtoClass === '') {
            $message = sprintf(
                'DTO class not specified for \'%s\' resource',
                static::class
            );

            throw new UnexpectedValueException($message);
        }

        return $this->dtoClass;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function setDtoClass(string $dtoClass): self
    {
        $this->dtoClass = $dtoClass;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getEntityName(): string
    {
        return $this->getRepository()->getEntityName();
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getReference(string $id, ?string $entityManagerName = null): ?object
    {
        return $this->getRepository()->getReference($id, $entityManagerName);
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getAssociations(?string $entityManagerName = null): array
    {
        return array_keys($this->getRepository()->getAssociations($entityManagerName));
    }

    /**
     * {@inheritdoc}
     */
    #[Override]
    public function getDtoForEntity(
        string $id,
        string $dtoClass,
        RestDtoInterface $dto,
        ?bool $patch = null,
        ?string $entityManagerName = null
    ): RestDtoInterface {
        $patch ??= false;
        // Fetch entity
        $entity = $this->getEntity($id, $entityManagerName);

        /**
         * Create new instance of DTO and load entity to that.
         *
         * @var RestDtoInterface $restDto
         * @var class-string<RestDtoInterface> $dtoClass
         */
        $restDto = new $dtoClass()
            ->setId($id);

        if ($patch === true) {
            $restDto->load($entity);
        }

        $restDto->patch($dto);

        return $restDto;
    }

    /**
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    protected function getEntity(string $id, ?string $entityManagerName): EntityInterface
    {
        $entity = $this->getRepository()->find(id: $id, entityManagerName: $entityManagerName);

        if ($entity === null) {
            throw new NotFoundHttpException('Not found');
        }

        return $entity;
    }

    /**
     * Helper method to validate given DTO class.
     *
     * @throws Throwable
     */
    protected function validateDto(RestDtoInterface $dto, bool $skipValidation): void
    {
        /** @var ConstraintViolationListInterface|null $errors */
        $errors = $skipValidation ? null : $this->getValidator()->validate($dto);

        // Oh noes, we have some errors
        if ($errors !== null && $errors->count() > 0) {
            throw new ValidatorException($dto::class, $errors);
        }
    }

    /**
     * Method to validate specified entity.
     *
     * @throws Throwable
     */
    protected function validateEntity(EntityInterface $entity, bool $skipValidation): void
    {
        $errors = $skipValidation ? null : $this->getValidator()->validate($entity);

        // Oh noes, we have some errors
        if ($errors !== null && $errors->count() > 0) {
            throw new ValidatorException($entity::class, $errors);
        }
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function checkThatEntityExists(bool $throwExceptionIfNotFound, ?EntityInterface $entity): void
    {
        // Entity not found
        if ($throwExceptionIfNotFound && $entity === null) {
            throw new NotFoundHttpException('Not found');
        }
    }
}
