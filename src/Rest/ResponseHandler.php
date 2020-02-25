<?php
declare(strict_types = 1);
/**
 * /src/Rest/ResponseHandler.php
 */

namespace App\Rest;

use App\Rest\Interfaces\ResponseHandlerInterface;
use Exception;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Rest\Interfaces\RestResourceInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ResponseHandler
 *
 * @package App\Rest
 */
final class ResponseHandler implements ResponseHandlerInterface
{
    /**
     * Content types for supported response output formats.
     *
     * @var array<string, string>
     */
    private array $contentTypes = [
        self::FORMAT_JSON => 'application/json',
        self::FORMAT_XML => 'application/xml',
    ];
    private SerializerInterface $serializer;

    /**
     * Constructor
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Getter for serializer
     *
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * Helper method to get serialization context for request.
     *
     * @param Request                    $request
     * @param RestResourceInterface|null $restResource
     *
     * @return array
     */
    public function getSerializeContext(Request $request, ?RestResourceInterface $restResource = null): array
    {
        /**
         * Specify used populate settings
         *
         * @var array
         */
        $populate = (array)$request->get('populate', []);
        $groups = array_merge(['default', $populate]);

        if ($restResource !== null) {
            // Get current entity name
            $bits = explode('\\', $restResource->getEntityName());
            $entityName = (string)end($bits);
            $populate = $this->checkPopulateAll(
                array_key_exists('populateAll', $request->query->all()),
                $populate,
                $entityName,
                $restResource
            );
            $groups = array_merge([$entityName], $populate);
            $filter = fn (string $groupName): bool => strncmp($groupName, 'Set.', 4) === 0;

            if (array_key_exists('populateOnly', $request->query->all())
                || count(array_filter($groups, $filter)) > 0
            ) {
                $groups = count($populate) === 0 ? [$entityName] : $populate;
            }
        }

        return array_merge(
            ['groups' => $groups],
            $restResource !== null ? $restResource->getSerializerContext() : [],
        );
    }

    /**
     * Helper method to create response for request.
     *
     * @param Request                    $request
     * @param mixed                      $data
     * @param RestResourceInterface|null $restResource
     * @param int|null                   $httpStatus
     * @param string|null                $format
     * @param array|null                 $context
     *
     * @return Response
     *
     * @throws HttpException
     */
    public function createResponse(
        Request $request,
        $data,
        ?RestResourceInterface $restResource = null,
        ?int $httpStatus = null,
        ?string $format = null,
        ?array $context = null
    ): Response {
        $httpStatus ??= Response::HTTP_OK;
        $context ??= $this->getSerializeContext($request, $restResource);
        $format = $this->getFormat($request, $format);
        // Get response
        $response = $this->getResponse($data, $httpStatus, $format, $context);
        // Set content type
        $response->headers->set('Content-Type', $this->contentTypes[$format]);

        return $response;
    }

    /**
     * Method to handle form errors.
     *
     * @param FormInterface $form
     *
     * @throws HttpException
     */
    public function handleFormError(FormInterface $form): void
    {
        $errors = [];

        /** @var FormError $error */
        foreach ($form->getErrors(true) as $error) {
            $origin = $error->getOrigin();
            $name = $origin !== null ? $origin->getName() : '';
            $errors[] = sprintf(
                'Field \'%s\': %s',
                $name,
                $error->getMessage()
            );

            if ($name === '') {
                array_pop($errors);
                $errors[] = $error->getMessage();
            }
        }

        throw new HttpException(Response::HTTP_BAD_REQUEST, implode("\n", $errors));
    }

    /**
     * @param bool                  $populateAll
     * @param array                 $populate
     * @param string                $entityName
     * @param RestResourceInterface $restResource
     *
     * @return array
     */
    private function checkPopulateAll(
        bool $populateAll,
        array $populate,
        string $entityName,
        RestResourceInterface $restResource
    ): array {
        // Set all associations to be populated
        if ($populateAll && count($populate) === 0) {
            $associations = $restResource->getAssociations();
            $populate = array_map(fn (string $assocName): string => $entityName . '.' . $assocName, $associations);
        }

        return $populate;
    }

    /**
     * @param Request     $request
     * @param string|null $format
     *
     * @return string
     */
    private function getFormat(Request $request, ?string $format = null): string
    {
        return $format ?? ($request->getContentType() === self::FORMAT_XML ? self::FORMAT_XML : self::FORMAT_JSON);
    }

    /**
     * @param mixed   $data
     * @param int     $httpStatus
     * @param string  $format
     * @param array   $context
     *
     * @return Response
     *
     * @throws HttpException
     */
    private function getResponse($data, int $httpStatus, string $format, array $context): Response
    {
        try {
            // Create new response
            $response = new Response();
            $response->setContent($this->serializer->serialize($data, $format, $context));
            $response->setStatusCode($httpStatus);
        } catch (Exception $exception) {
            $status = Response::HTTP_BAD_REQUEST;

            throw new HttpException($status, $exception->getMessage(), $exception, [], $status);
        }

        return $response;
    }
}
