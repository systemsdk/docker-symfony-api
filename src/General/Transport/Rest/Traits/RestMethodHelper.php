<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Interfaces\ControllerInterface;
use App\General\Transport\Rest\Traits\Methods\RestMethodProcessCriteria;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\UnitOfWork;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use UnexpectedValueException;

use function array_key_exists;
use function class_implements;
use function in_array;
use function is_array;
use function is_int;
use function sprintf;

/**
 * @package App\General
 */
trait RestMethodHelper
{
    use RestMethodProcessCriteria;

    /**
     * Method + DTO class names (key + value)
     *
     * @var array<string, string>
     */
    protected static array $dtoClasses = [];

    /**
     * Getter method for used DTO class for current controller.
     *
     * @throws UnexpectedValueException
     */
    public function getDtoClass(?string $method = null): string
    {
        $dtoClass = $method !== null && array_key_exists($method, static::$dtoClasses)
            ? static::$dtoClasses[$method]
            : $this->getResource()->getDtoClass();

        $interfaces = class_implements($dtoClass);

        if (is_array($interfaces) && !in_array(RestDtoInterface::class, $interfaces, true)) {
            $message = sprintf(
                'Given DTO class \'%s\' is not implementing \'%s\' interface.',
                $dtoClass,
                RestDtoInterface::class,
            );

            throw new UnexpectedValueException($message);
        }

        return $dtoClass;
    }

    /**
     * Method to validate REST trait method.
     *
     * @param array<int, string> $allowedHttpMethods
     *
     * @throws LogicException
     * @throws MethodNotAllowedHttpException
     */
    public function validateRestMethod(Request $request, array $allowedHttpMethods): void
    {
        // Make sure that we have everything we need to make this work
        if (!($this instanceof ControllerInterface)) {
            $message = sprintf(
                'You cannot use \'%s\' controller class with REST traits if that does not implement \'%s\'',
                static::class,
                ControllerInterface::class,
            );

            throw new LogicException($message);
        }

        if (!in_array($request->getMethod(), $allowedHttpMethods, true)) {
            throw new MethodNotAllowedHttpException($allowedHttpMethods);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws Throwable
     */
    public function handleRestMethodException(
        Throwable $exception,
        ?string $id = null,
        ?string $entityManagerName = null
    ): Throwable {
        if ($id !== null) {
            $this->detachEntityFromManager($id, $entityManagerName);
        }

        return $this->determineOutputAndStatusCodeForRestMethodException($exception);
    }

    /**
     * Getter method for exception code with fallback to `400` bad response.
     */
    private function getExceptionCode(Throwable $exception): int
    {
        $code = $exception->getCode();

        return is_int($code) && $code !== 0 ? $code : Response::HTTP_BAD_REQUEST;
    }

    /**
     * Method to detach entity from entity manager so possible changes to it won't be saved.
     *
     * @throws Throwable
     */
    private function detachEntityFromManager(string $id, ?string $entityManagerName): void
    {
        $currentResource = $this->getResource();
        $entityManager = $currentResource->getRepository()->getEntityManager($entityManagerName);
        // Fetch entity
        $entity = $currentResource->getRepository()->find(id: $id, entityManagerName: $entityManagerName);

        // Detach entity from manager if it's been managed by it
        if (
            $entity !== null
            /* @scrutinizer ignore-call */
            && $entityManager->getUnitOfWork()->getEntityState($entity) === UnitOfWork::STATE_MANAGED
        ) {
            $entityManager->clear();
        }
    }

    private function determineOutputAndStatusCodeForRestMethodException(Throwable $exception): Throwable
    {
        $code = $this->getExceptionCode($exception);
        $output = new HttpException($code, $exception->getMessage(), $exception, [], $code);

        if ($exception instanceof NoResultException || $exception instanceof NotFoundHttpException) {
            $code = Response::HTTP_NOT_FOUND;
            $output = new HttpException($code, 'Not found', $exception, [], $code);
        } elseif ($exception instanceof NonUniqueResultException) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
            $output = new HttpException($code, $exception->getMessage(), $exception, [], $code);
        } elseif ($exception instanceof HttpException) {
            if ($exception->getCode() === 0) {
                $output = new HttpException(
                    $exception->getStatusCode(),
                    $exception->getMessage(),
                    $exception->getPrevious(),
                    $exception->getHeaders(),
                    $code,
                );
            } else {
                $output = $exception;
            }
        }

        return $output;
    }
}
