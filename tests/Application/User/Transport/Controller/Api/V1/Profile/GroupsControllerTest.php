<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\Profile;

use App\General\Domain\Utils\JSON;
use App\Role\Domain\Enum\Role;
use App\Tests\TestCase\WebTestCase;
use App\User\Infrastructure\DataFixtures\ORM\LoadUserGroupData;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class GroupsControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/profile';

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/profile/groups` for the `john-logged` user returns success response.')]
    public function testThatGetGroupsActionForUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-logged', 'password-logged');

        $client->request(method: 'GET', uri: $this->baseUrl . '/groups');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertCount(1, $responseData);
        self::assertArrayHasKey('id', $responseData[0]);
        self::assertArrayHasKey('role', $responseData[0]);
        self::assertArrayHasKey('name', $responseData[0]);
        self::assertEquals($responseData[0]['id'], LoadUserGroupData::getUuidByKey('Role-logged'));
        self::assertIsArray($responseData[0]['role']);
        self::assertArrayHasKey('id', $responseData[0]['role']);
        self::assertEquals(Role::LOGGED->value, $responseData[0]['role']['id']);
        self::assertIsString($responseData[0]['name']);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/profile/groups` for non-logged user returns error response.')]
    public function testThatGetGroupsActionForNonLoggedUserReturnsErrorResponse(): void
    {
        $client = $this->getTestClient();

        $client->request(method: 'GET', uri: $this->baseUrl . '/groups');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }
}
