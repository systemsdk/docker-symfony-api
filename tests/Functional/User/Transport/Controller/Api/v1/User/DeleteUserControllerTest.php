<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\User;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Domain\Entity\Role;
use App\User\Application\DTO\User\UserCreate;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class DeleteUserControllerTest
 *
 * @package App\Tests
 */
class DeleteUserControllerTest extends WebTestCase
{
    private const USERNAME_FOR_TEST = 'test-user';
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user';
    private User $user;
    private UserResource $userResource;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        // let's create user that we will use within this test
        $userResource = static::getContainer()->get(UserResource::class);
        self::assertInstanceOf(UserResource::class, $userResource);
        $this->userResource = $userResource;
        $user = $this->userResource->findOneBy([
            'username' => self::USERNAME_FOR_TEST,
        ]);

        if (!$user) {
            $userGroupResource = static::getContainer()->get(UserGroupResource::class);
            self::assertInstanceOf(UserGroupResource::class, $userGroupResource);
            $userGroupForAttach = $userGroupResource->findOneBy([
                'role' => Role::ROLE_LOGGED,
            ]);
            self::assertInstanceOf(UserGroup::class, $userGroupForAttach);
            $dto = (new UserCreate())
                ->setUsername(self::USERNAME_FOR_TEST)
                ->setFirstName('Test')
                ->setLastName('Tester')
                ->setEmail(self::USERNAME_FOR_TEST . '@test.com')
                ->setUserGroups([$userGroupForAttach])
                ->setPassword('test12345');
            $user = $this->userResource->create($dto);
            self::assertInstanceOf(User::class, $user);
        }
        $this->user = $user;
    }

    /**
     * @testdox Test that `DELETE /api/v1/user/{userId}` under the non-root user returns error response.
     *
     * @throws Throwable
     */
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
     * @testdox Test that `DELETE /api/v1/user/{userId}` for the root user returns success response.
     *
     * @throws Throwable
     */
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
