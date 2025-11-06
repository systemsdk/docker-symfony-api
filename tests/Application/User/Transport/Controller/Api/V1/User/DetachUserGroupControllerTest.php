<?php

declare(strict_types=1);

namespace App\Tests\Application\User\Transport\Controller\Api\V1\User;

use App\General\Domain\Utils\JSON;
use App\Role\Domain\Enum\Role;
use App\Tests\TestCase\WebTestCase;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class DetachUserGroupControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user';
    private User $user;
    private UserGroup $userGroupForAttachAndDetach;
    private UserResource $userResource;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userResource = static::getContainer()->get(UserResource::class);
        $userGroupResource = static::getContainer()->get(UserGroupResource::class);
        $user = $this->userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $user);
        $this->user = $user;
        // let's attach role to the user in order to detach it in the test bellow
        $userGroupForAttachAndDetach = $userGroupResource->findOneBy([
            'role' => Role::LOGGED->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupForAttachAndDetach);
        $this->userGroupForAttachAndDetach = $userGroupForAttachAndDetach;
        $this->user = $this->userResource->save($this->user->addUserGroup($this->userGroupForAttachAndDetach), false);
        $userGroupResource->save($this->userGroupForAttachAndDetach, true, true);
        self::assertEquals(2, $this->user->getUserGroups()->count());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `DELETE /api/v1/user/{userId}/group/{groupId}` under the non-root user returns error.')]
    public function testThatDetachUserGroupFromTheUserUnderNonRootUserReturnsErrorResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('DELETE', $this->baseUrl . '/' . $this->user->getId() . '/group/'
            . $this->userGroupForAttachAndDetach->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode(), "Response:\n" . $response);

        // let's check that inside database we have the same data as before request
        /** @var User|null $user */
        $user = $this->userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(2, $user->getUserGroups()->count());
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `DELETE /api/v1/user/{userId}/group/{groupId}` under the root user returns success.')]
    public function testThatDetachUserGroupFromTheUserUnderRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('DELETE', $this->baseUrl . '/' . $this->user->getId() . '/group/'
            . $this->userGroupForAttachAndDetach->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_OK, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertCount(1, $responseData);
        foreach ($responseData as $userGroup) {
            self::assertIsArray($userGroup);
            self::assertArrayHasKey('id', $userGroup);
            self::assertArrayHasKey('role', $userGroup);
            self::assertIsArray($userGroup['role']);
            self::assertArrayHasKey('id', $userGroup['role']);
            self::assertContains($userGroup['role']['id'], [Role::USER->value]);
            self::assertArrayHasKey('name', $userGroup);
        }

        // let's check that inside database we have the same data as in response above
        /** @var User|null $user */
        $user = $this->userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(1, $user->getUserGroups()->count());
        /** @var UserGroup $userGroup */
        $userGroup = $user->getUserGroups()->first();
        self::assertEquals(Role::USER->value, $userGroup->getRole()->getId());
    }
}
