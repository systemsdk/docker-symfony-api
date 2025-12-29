<?php

declare(strict_types=1);

namespace App\Tool\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Uuid;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Override;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Throwable;

/**
 * @package App\Tool
 */
#[ORM\Entity]
#[ORM\Table(name: 'health')]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Health implements EntityInterface
{
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(
        name: 'id',
        type: UuidBinaryOrderedTimeType::NAME,
        unique: true,
    )]
    #[Groups([
        'Health',
        'Health.id',
    ])]
    #[OA\Property(type: 'string', format: 'uuid')]
    private UuidInterface $id;

    #[ORM\Column(
        name: 'timestamp',
        type: Types::DATETIME_IMMUTABLE,
    )]
    #[Groups([
        'Health',
        'Health.timestamp',
    ])]
    private DateTimeImmutable $timestamp;

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->timestamp = new DateTimeImmutable(timezone: new DateTimeZone('UTC'));
    }

    #[Override]
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

    #[Override]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
