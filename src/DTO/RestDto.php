<?php
declare(strict_types = 1);
/**
 * /src/DTO/RestDto.php
 */

namespace App\DTO;

use App\DTO\Interfaces\RestDtoInterface;
use App\Entity\Interfaces\EntityInterface;
use BadMethodCallException;
use LogicException;

/**
 * Class RestDto
 *
 * @package App\DTO
 */
abstract class RestDto implements RestDtoInterface
{
    /**
     * DTO property mappings to setter method.
     *
     * Example:
     *  static protected $mappings = [
     *      'someProperty' => 'methodInYourDtoClass',
     *  ]
     *
     * This will call below method in your DTO class:
     *  protected function methodInYourDtoClass($entity, $value)
     *
     * And in that method make all necessary that you need to set that specified value.
     *
     * @var array
     */
    protected static array $mappings = [];

    /**
     * @var string|null
     */
    protected $id;

    /**
     * An array of 'visited' setter properties of current dto.
     *
     * @var array
     */
    private $visited = [];


    /**
     * @param string $id
     *
     * @return RestDtoInterface
     */
    public function setId(string $id): RestDtoInterface
    {
        $this->setVisited('id');
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Getter method for visited setters. This is needed for dto patching.
     *
     * @return array
     */
    public function getVisited(): array
    {
        return array_filter($this->visited, static function (string $property) {
            return $property !== 'id';
        });
    }

    /**
     * Setter for visited data. This is needed for dto patching.
     *
     * @param string $property
     *
     * @return RestDtoInterface
     */
    public function setVisited(string $property): RestDtoInterface
    {
        $this->visited[] = $property;

        return $this;
    }

    /**
     * Method to update specified entity with DTO data.
     *
     * @param EntityInterface $entity
     *
     * @return EntityInterface
     */
    public function update(EntityInterface $entity): EntityInterface
    {
        foreach ($this->getVisited() as $property) {
            if (array_key_exists($property, static::$mappings)) {
                $this->{static::$mappings[$property]}($entity, $this->{$property});

                continue;
            }

            // Determine setter method
            $setter = 'set' . ucfirst($property);
            // Update current dto property value
            $entity->{$setter}($this->{$property});
        }

        return $entity;
    }

    /**
     * Method to patch current dto with another one.
     *
     * @param RestDtoInterface $dto
     *
     * @throws LogicException|BadMethodCallException
     *
     * @return RestDtoInterface
     */
    public function patch(RestDtoInterface $dto): RestDtoInterface
    {
        foreach ($dto->getVisited() as $property) {
            // Determine getter method
            $getter = $this->determineGetterMethod($dto, $property);
            // Determine setter method
            $setter = 'set' . ucfirst($property);
            // Update current dto property value
            $this->{$setter}($dto->{$getter}());
        }

        return $this;
    }

    /**
     * Method to determine used getter method for DTO property.
     *
     * @param RestDtoInterface $dto
     * @param string           $property
     *
     * @throws LogicException
     *
     * @return string
     */
    private function determineGetterMethod(RestDtoInterface $dto, string $property): string
    {
        $getter = $this->getGetterMethod($dto, $property);

        // Oh noes - required getter method does not exist
        if ($getter === null) {
            $message = sprintf(
                'DTO class \'%s\' does not have getter method property \'%s\' - cannot patch dto',
                get_class($dto),
                $property
            );

            throw new BadMethodCallException($message);
        }

        return $getter;
    }

    /**
     * @param RestDtoInterface $dto
     * @param string           $property
     *
     * @throws LogicException
     *
     * @return string|null
     */
    private function getGetterMethod(RestDtoInterface $dto, string $property): ?string
    {
        $getters = [
            'get' . ucfirst($property),
            'is' . ucfirst($property),
            'has' . ucfirst($property),
        ];
        $filter = static function (string $method) use ($dto): bool {
            return method_exists($dto, $method);
        };
        $getterMethods = array_filter($getters, $filter);

        return $this->validateGetterMethod($property, $getterMethods);
    }

    /**
     * @param string $property
     * @param array  $getterMethods
     *
     * @throws LogicException
     *
     * @return string|null
     */
    private function validateGetterMethod(string $property, array $getterMethods): ?string
    {
        if (count($getterMethods) > 1) {
            $message = sprintf(
                'Property \'%s\' has multiple getter methods - this is insane!',
                $property
            );

            throw new LogicException($message);
        }

        $method = current($getterMethods);

        return $method === false ? null : $method;
    }
}
