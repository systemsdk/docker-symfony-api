<?php
declare(strict_types = 1);
/**
 * /src/Request/ParamConverter/RestResourceConverter.php
 */

namespace App\Request\ParamConverter;

use App\Resource\ResourceCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

/** @noinspection AnnotationMissingUseInspection */
/** @noinspection PhpUndefinedClassInspection */
/**
 * Class RestResourceConverter
 *
 * This is meant to be used within controller actions that uses @ParamConverter annotation. Example:
 *  /**
 *   * @Route(
 *   *    "/{userEntity}",
 *   *    requirements={
 *   *        "userEntity" = "%app.uuid_v1_regex%",
 *   *    }
 *   * )
 *   *
 *   * @ParamConverter(
 *   *      "userEntity",
 *   *      class="App\Resource\UserResource",
 *   *  )
 *   *
 *   * @param User $collection
 *   *\/
 *  public function testAction(User $userEntity)
 *  {
 *      ...
 *  }
 *
 * Purpose of this param converter is to use exactly same methods and workflow as in basic REST API requests.
 *
 * @package App\Request\ParamConverter
 */
class RestResourceConverter implements ParamConverterInterface
{
    private ResourceCollection  $collection;

    /**
     * RestResourceConverter constructor.
     *
     * @param ResourceCollection $collection
     */
    public function __construct(ResourceCollection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @throws Throwable
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $name = $configuration->getName();
        $identifier = (string)$request->attributes->get($name, '');
        $resource = $this->collection->get($configuration->getClass());

        if ($identifier !== '') {
            $request->attributes->set($name, $resource->findOne($identifier, true));
        }

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration
     *
     * @return bool
     */
    public function supports(ParamConverter $configuration): bool
    {
        return $this->collection->has($configuration->getClass());
    }
}
