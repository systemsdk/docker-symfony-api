<?php

declare(strict_types=1);

namespace App\General\Transport\Request\ParamConverter;

use App\General\Application\Resource\ResourceCollection;
use App\General\Application\Rest\Interfaces\RestFindOneResourceInterface;
use App\General\Application\Rest\Interfaces\RestResourceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

use function assert;
use function is_string;

/**
 * Class RestResourceConverter
 *
 * Purpose of this param converter is to use exactly same methods and workflow as in basic REST API requests.
 *
 * @package App\General
 */
class RestResourceConverter implements ParamConverterInterface
{
    public function __construct(
        private readonly ResourceCollection $collection,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $name = $configuration->getName();
        $identifier = $request->attributes->get($name, '');
        $class = $configuration->getClass();
        assert(is_string($identifier) && is_string($class));
        /** @var RestResourceInterface|RestFindOneResourceInterface $resource */
        $resource = $this->collection->get($class, RestFindOneResourceInterface::class);

        if ($identifier !== '') {
            $request->attributes->set($name, $resource->findOne($identifier, true));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $this->collection->has($configuration->getClass(), RestFindOneResourceInterface::class);
    }
}
