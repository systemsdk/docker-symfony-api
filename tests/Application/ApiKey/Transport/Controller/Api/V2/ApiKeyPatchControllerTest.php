<?php

declare(strict_types=1);

namespace App\Tests\Application\ApiKey\Transport\Controller\Api\V2;

use App\ApiKey\Application\Resource\ApiKeyFindOneResource;
use App\ApiKey\Domain\Entity\ApiKey;
use App\Tests\Application\ApiKey\Transport\Controller\Api\V2\Traits\ApiKeyHelper;
use App\Tests\TestCase\WebTestCase;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Throwable;

/**
 * @package App\Tests
 */
class ApiKeyPatchControllerTest extends WebTestCase
{
    use ApiKeyHelper;

    private string $baseUrl = self::API_URL_PREFIX . '/v2/api_key';
    private ApiKey $apiKey;
    private ApiKeyFindOneResource $apiKeyFindOneResource;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->apiKeyFindOneResource = static::getContainer()->get(ApiKeyFindOneResource::class);
        /** @var ApiKey $apiKey */
        $apiKey = $this->apiKeyFindOneResource->findOneBy(
            criteria: [
                'description' => 'ApiKey Description: api',
            ],
            throwExceptionIfNotFound: true
        );
        $this->apiKey = $apiKey;
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PATCH /v2/api_key/{id}` returns forbidden error for non-root user.')]
    public function testThatPatchActionForNonRootUserReturnsForbiddenResponse(): void
    {
        $this->checkActionForNonRootUserReturnsForbiddenResponse('PATCH', 'test api key patched');
    }

    /**
     * @param array<string, string|array<string>> $requestData
     *
     * @throws Throwable
     */
    #[DataProvider('dataProviderWithIncorrectData')]
    #[TestDox('Test that `PATCH /v2/api_key/{id}` with wrong data returns validation error.')]
    public function testThatPatchActionForRootUserWithWrongDataReturnsValidationErrorResponse(
        array $requestData,
        string $error
    ): void {
        $this->checkActionForRootUserWithWrongDataReturnsValidationErrorResponse('PATCH', $requestData, $error);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `PATCH /v2/api_key/{id}` for the Root user returns success response.')]
    public function testThatPatchActionForRootUserReturnsSuccessResponse(): void
    {
        $this->checkActionForRootUserReturnsSuccessResponse('PATCH', 'test api key patched');
    }

    /**
     * @return Generator<array{0: array<string, string|array<string>>, 1: string}>
     */
    public static function dataProviderWithIncorrectData(): Generator
    {
        yield [
            [
                'description' => '',
                'userGroups' => [
                    LoadUserGroupData::getUuidByKey('Role-api'),
                ],
            ],
            'This value should not be blank.',
        ];
        yield [
            [
                'description' => 'test api key patched',
                'userGroups' => [
                    '90000000-0000-1000-8000-900000000009',
                ],
            ],
            'id(90000000-0000-1000-8000-900000000009) was not found',
        ];
    }
}
