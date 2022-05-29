<?php

declare(strict_types=1);

namespace App\General\Transport\ArgumentResolver;

use App\General\Application\Resource\ResourceCollection;
use App\General\Application\Rest\Interfaces\RestFindOneResourceInterface;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Throwable;

use function is_string;
use function is_subclass_of;

/**
 * Class EntityValueResolver
 *
 * Example how to use this within your controller;
 *
 *  #[Route(path: 'some_path_to_your_route/{user}/{apikey}')]
 *  public function someMethod(\App\User\Domain\Entity\User $user, \App\ApiKey\Domain\Entity\ApiKey $apikey): Response
 *  {
 *      ...
 *  }
 *
 * And when you make your request like `GET /some_path_to_your_route/_user_uuid_/_apikey_uuid`
 * you will get those entities to your controller method which are resolved automatically via
 * those entity resource classes.
 *
 * Only thing that you need check is that parameter in your `path` definition matches with
 * method argument name.
 *
 * @package App\General
 */
class EntityValueResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private readonly ResourceCollection $resourceCollection,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_string($this->getUuid($argument, $request))
            && is_subclass_of((string)$argument->getType(), EntityInterface::class, true)
            && $this->resourceCollection->hasEntityResource($argument->getType(), RestFindOneResourceInterface::class);
    }

    /**
     * {@inheritdoc}
     *
     * @return Generator<EntityInterface|null>
     *
     * @throws Throwable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->resourceCollection /** @phpstan-ignore-line */
            ->getEntityResource((string)$argument->getType(), RestFindOneResourceInterface::class)
            ->findOne((string)($this->getUuid($argument, $request)), !$argument->isNullable());
    }

    private function getUuid(ArgumentMetadata $argument, Request $request): mixed
    {
        $argumentName = $argument->getName();

        return $request->attributes->get($argumentName)
            ?? $request->request->get($argumentName)
            ?? $request->query->get($argumentName);
    }
}
