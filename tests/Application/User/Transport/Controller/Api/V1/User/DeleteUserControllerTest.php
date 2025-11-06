<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\User;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class DeleteUserControllerTest extends WebTestCase
{
    private const string USERNAME_FOR_TEST = 'john';
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user';
    private User $user;
    private UserResource $userResource;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userResource = static::getContainer()->get(UserResource::class);
        /** @var User $user */
        $user = $this->userResource->findOneBy(
            criteria: [
                'username' => self::USERNAME_FOR_TEST,
            ],
            throwExceptionIfNotFound: true
        );
        $this->user = $user;
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `DELETE /api/v1/user/{userId}` under the non-root user returns error response.')]
    public function testThatDeleteActionForNonRootUserReturnsForbiddenResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request(method: 'DELETE', uri: $this->baseUrl . '/' . $this->user->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);

        // let's check that row wasn't deleted inside db.
        /** @var User|null $user */
        $user = $this->userResource->findOne($this->user->getId());
        self::assertInstanceOf(User::class, $user);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `DELETE /api/v1/user/{userId}` for the root user returns success response.')]
    public function testThatDeleteActionForRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('DELETE', $this->baseUrl . '/' . $this->user->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertArrayHasKey('id', $responseData);
        self::assertArrayHasKey('username', $responseData);
        self::assertArrayHasKey('firstName', $responseData);
        self::assertArrayHasKey('lastName', $responseData);
        self::assertArrayHasKey('email', $responseData);
        self::assertArrayHasKey('language', $responseData);
        self::assertArrayHasKey('locale', $responseData);
        self::assertArrayHasKey('timezone', $responseData);
        self::assertEquals($this->user->getId(), $responseData['id']);

        // let's check that row deleted inside db.
        /** @var User|null $user */
        $user = $this->userResource->findOne($this->user->getId());
        self::assertNull($user);
    }
}
