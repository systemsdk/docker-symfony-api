<?php

declare(strict_types=1);

namespace App\Request\ParamConverter;

use App\Resource\ResourceCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/**
 * Class RestResourceConverter
 *
 * Purpose of this param converter is to use exactly same methods and workflow as in basic REST API requests.
 *
 * @package App\Request\ParamConverter
 */
class RestResourceConverter implements ParamConverterInterface
{
    public function __construct(
        private ResourceCollection $collection,
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
        $identifier = (string)$request->attributes->get($name, '');
        $resource = $this->collection->get((string)$configuration->getClass());

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
        return $this->collection->has($configuration->getClass());
    }
}
