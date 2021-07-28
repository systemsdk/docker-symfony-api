<?php

declare(strict_types=1);

namespace App\Rest;

use App\DTO\Interfaces\RestDtoInterface;
use App\Repository\Interfaces\BaseRepositoryInterface;
use App\Rest\Interfaces\RestResourceInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use UnexpectedValueException;

use function array_keys;
use function sprintf;

/**
 * Class RestResource
 *
 * @package App\Rest
 */
abstract class RestResource implements RestResourceInterface
{
    use Traits\RestResourceBaseMethods;

    private ValidatorInterface $validator;
    private string $dtoClass = '';

    /**
     * {@inheritdoc}
     */
    public function getSerializerContext(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(): BaseRepositoryInterface
    {
        $exception = new UnexpectedValueException('Repository not set on constructor');

        return property_exists($this, 'repository') ? $this->repository ?? throw $exception : throw $exception;
    }

    /**
     * {@inheritdoc}
     */
    public function setRepository(BaseRepositoryInterface $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValidator(): ValidatorInterface
    {
        return $this->validator;
    }

    /**
     * {@inheritdoc}
     *
     * @see https://symfony.com/doc/current/service_container/autowiring.html#autowiring-other-methods-e-g-setters
     *
     * @required
     */
    public function setValidator(ValidatorInterface $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
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
    public function setDtoClass(string $dtoClass): RestResourceInterface
    {
        $this->dtoClass = $dtoClass;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityName(): string
    {
        return $this->getRepository()->getEntityName();
    }

    /**
     * {@inheritdoc}
     */
    public function getReference(string $id): ?object
    {
        return $this->getRepository()->getReference($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getAssociations(): array
    {
        return array_keys($this->getRepository()->getAssociations());
    }

    /**
     * {@inheritdoc}
     */
    public function getDtoForEntity(
        string $id,
        string $dtoClass,
        RestDtoInterface $dto,
        ?bool $patch = null
    ): RestDtoInterface {
        $patch ??= false;
        // Fetch entity
        $entity = $this->getEntity($id);

        /**
         * Create new instance of DTO and load entity to that.
         *
         * @var RestDtoInterface $restDto
         * @var class-string<RestDtoInterface> $dtoClass
         */
        $restDto = (new $dtoClass())
            ->setId($id);

        if ($patch === true) {
            $restDto->load($entity);
        }

        $restDto->patch($dto);

        return $restDto;
    }
}
