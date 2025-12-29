<?php

declare(strict_types=1);

namespace App\Log\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Throwable;

/**
 * @package App\Log
 */
#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'log_login_failure')]
#[ORM\Index(
    name: 'user_id',
    columns: ['user_id'],
)]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class LogLoginFailure implements EntityInterface
{
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(
        name: 'id',
        type: UuidBinaryOrderedTimeType::NAME,
        unique: true,
    )]
    #[Groups([
        'LogLoginFailure',
        'LogLoginFailure.id',
    ])]
    private UuidInterface $id;

    #[ORM\Column(
        name: 'timestamp',
        type: Types::DATETIME_IMMUTABLE,
    )]
    #[Groups([
        'LogLoginFailure',
        'LogLoginFailure.timestamp',
    ])]
    private DateTimeImmutable $timestamp;

    /**
     * @throws Throwable
     */
    public function __construct(
        #[ORM\ManyToOne(
            targetEntity: User::class,
            inversedBy: 'logsLoginFailure',
        )]
        #[ORM\JoinColumn(
            name: 'user_id',
            nullable: false,
            onDelete: 'CASCADE',
        )]
        #[Groups([
            'LogLoginFailure',
            'LogLoginFailure.user',
        ])]
        private readonly User $user
    ) {
        $this->id = $this->createUuid();
        $this->timestamp = new DateTimeImmutable(timezone: new DateTimeZone('UTC'));
    }

    #[Override]
    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTimestamp(): DateTimeImmutable
    {
        return $this->getCreatedAt();
    }

    #[Override]
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->timestamp;
    }
}
