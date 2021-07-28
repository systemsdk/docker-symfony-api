<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Interfaces\EntityInterface;
use App\Entity\Traits\Uuid;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Throwable;

/**
 * Class Health
 *
 * @ORM\Table(
 *      name="health",
 *  )
 * @ORM\Entity()
 *
 * @package App\Entity
 */
class Health implements EntityInterface
{
    // Traits
    use Uuid;

    /**
     * @Groups({
     *      "Health",
     *      "Health.id",
     *  })
     *
     * @ORM\Column(
     *      name="id",
     *      type="uuid_binary_ordered_time",
     *      unique=true,
     *      nullable=false,
     *  )
     * @ORM\Id()
     *
     * @OA\Property(type="string", format="uuid")
     */
    private UuidInterface $id;

    /**
     * @Groups({
     *      "Health",
     *      "Health.timestamp",
     *  })
     *
     * @ORM\Column(
     *      name="timestamp",
     *      type="datetime_immutable",
     *      nullable=false,
     *  )
     */
    private DateTimeImmutable $timestamp;

    /**
     * Constructor
     *
     * @throws Throwable
     */
    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->timestamp = new DateTimeImmutable(timezone: new DateTimeZone('UTC'));
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->getCreatedAt();
    }

    public function setTimestamp(DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
