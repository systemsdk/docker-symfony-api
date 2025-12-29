<?php

declare(strict_types=1);

namespace App\Log\Domain\Entity;

use App\ApiKey\Domain\Entity\ApiKey;
use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Uuid;
use App\Log\Domain\Entity\Traits\LogEntityTrait;
use App\Log\Domain\Entity\Traits\LogRequestProcessRequestTrait;
use App\User\Domain\Entity\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes as OA;
use Override;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Attribute\Groups;
use Throwable;

use function mb_strlen;

/**
 * @package App\Log
 */
#[ORM\Entity(readOnly: true)]
#[ORM\Table(name: 'log_request')]
#[ORM\Index(
    name: 'user_id',
    columns: ['user_id'],
)]
#[ORM\Index(
    name: 'api_key_id',
    columns: ['api_key_id'],
)]
#[ORM\Index(
    name: 'request_date',
    columns: ['date'],
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class LogRequest implements EntityInterface
{
    use LogEntityTrait;
    use LogRequestProcessRequestTrait;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(
        name: 'id',
        type: UuidBinaryOrderedTimeType::NAME,
        unique: true,
    )]
    #[Groups([
        'LogRequest',
        'LogRequest.id',

        'ApiKey.logsRequest',
        'User.logsRequest',
    ])]
    #[OA\Property(type: 'string', format: 'uuid')]
    private UuidInterface $id;

    #[ORM\Column(
        name: 'status_code',
        type: Types::INTEGER,
    )]
    #[Groups([
        'LogRequest',
        'LogRequest.statusCode',
    ])]
    private int $statusCode = 0;

    #[ORM\Column(
        name: 'response_content_length',
        type: Types::INTEGER,
    )]
    #[Groups([
        'LogRequest',
        'LogRequest.responseContentLength',
    ])]
    private int $responseContentLength = 0;

    #[ORM\Column(
        name: 'is_main_request',
        type: Types::BOOLEAN,
    )]
    #[Groups([
        'LogRequest',
        'LogRequest.isMainRequest',
    ])]
    private bool $mainRequest;

    /**
     * @param array<int, string> $sensitiveProperties
     *
     * @throws Throwable
     */
    public function __construct(
        private readonly array $sensitiveProperties,
        ?Request $request = null,
        ?Response $response = null,
        #[ORM\ManyToOne(
            targetEntity: User::class,
            inversedBy: 'logsRequest',
        )]
        #[ORM\JoinColumn(
            name: 'user_id',
            onDelete: 'SET NULL',
        )]
        #[Groups([
            'LogRequest.user',
        ])]
        private readonly ?User $user = null,
        #[ORM\ManyToOne(
            targetEntity: ApiKey::class,
            inversedBy: 'logsRequest',
        )]
        #[ORM\JoinColumn(
            name: 'api_key_id',
            onDelete: 'SET NULL',
        )]
        #[Groups([
            'LogRequest.apiKey',
        ])]
        private readonly ?ApiKey $apiKey = null,
        ?bool $mainRequest = null
    ) {
        $this->id = $this->createUuid();
        $this->mainRequest = $mainRequest ?? true;

        $this->processTimeAndDate();

        if ($request !== null) {
            $this->processRequestData($request);
            $this->processRequest($request);
        }

        if ($response !== null) {
            $this->processResponse($response);
        }
    }

    #[Override]
    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseContentLength(): int
    {
        return $this->responseContentLength;
    }

    public function getApiKey(): ?ApiKey
    {
        return $this->apiKey;
    }

    public function isMainRequest(): bool
    {
        return $this->mainRequest;
    }

    /**
     * @return array<int, string>
     */
    public function getSensitiveProperties(): array
    {
        return $this->sensitiveProperties;
    }

    private function processResponse(Response $response): void
    {
        $content = $response->getContent();
        $this->statusCode = $response->getStatusCode();
        $this->responseContentLength = $content === false ? 0 : mb_strlen($content);
    }
}
