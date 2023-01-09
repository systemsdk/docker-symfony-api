<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\User;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Domain\Enum\Role;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class AttachUserGroupControllerTest
 *
 * @package App\Tests
 */
class AttachUserGroupControllerTest extends WebTestCase
{
    private string $baseUrl = self::API_URL_PREFIX . '/v1/user';
    private User $user;
    private UserGroup $userGroupForAttach;
    private UserResource $userResource;
    private UserGroupResource $userGroupResource;

    /**
     * @throws Throwable
     */
    protected function setUp(): void
    {
        parent::setUp();

        $userResource = static::getContainer()->get(UserResource::class);
        $userGroupResource = static::getContainer()->get(UserGroupResource::class);
        self::assertInstanceOf(UserResource::class, $userResource);
        self::assertInstanceOf(UserGroupResource::class, $userGroupResource);
        $this->userResource = $userResource;
        $this->userGroupResource = $userGroupResource;
        $user = $this->userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $user);
        $this->user = $user;
        // let's check that before running test the user has only 1 attached user group
        self::assertEquals(1, $this->user->getUserGroups()->count());
        $userGroup = $this->user->getUserGroups()->first();
        self::assertInstanceOf(UserGroup::class, $userGroup);
        self::assertEquals(Role::USER->value, $userGroup->getRole()->getId());
        $userGroupForAttach = $this->userGroupResource->findOneBy([
            'role' => Role::LOGGED->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupForAttach);
        $this->userGroupForAttach = $userGroupForAttach;
    }

    /**
     * @testdox Test that `POST /api/v1/user/{userId}/group/{groupId}` under the non-root user returns error response.
     *
     * @throws Throwable
     */
    public function testThatAttachUserGroupToTheUserUnderNonRootUserReturnsErrorResponse(): void
    {
        $client = $this->getTestClient('john-admin', 'password-admin');

        $client->request('POST', $this->baseUrl . '/' . $this->user->getId() . '/group/'
            . $this->userGroupForAttach->getId());
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
        self::assertEquals(1, $user->getUserGroups()->count());
    }

    /**
     * @testdox Test that `POST /api/v1/user/{userId}/group/{groupId}` under the root user returns success response.
     *
     * @throws Throwable
     */
    public function testThatAttachUserGroupToTheUserUnderRootUserReturnsSuccessResponse(): void
    {
        $client = $this->getTestClient('john-root', 'password-root');

        $client->request('POST', $this->baseUrl . '/' . $this->user->getId() . '/group/'
            . $this->userGroupForAttach->getId());
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_CREATED, $response->getStatusCode(), "Response:\n" . $response);
        $responseData = JSON::decode($content, true);
        self::assertIsArray($responseData);
        self::assertCount(2, $responseData);
        foreach ($responseData as $userGroup) {
            self::assertIsArray($userGroup);
            self::assertArrayHasKey('id', $userGroup);
            self::assertArrayHasKey('role', $userGroup);
            self::assertIsArray($userGroup['role']);
            self::assertArrayHasKey('id', $userGroup['role']);
            self::assertContains($userGroup['role']['id'], [Role::USER->value, Role::LOGGED->value]);
            self::assertArrayHasKey('name', $userGroup);
        }

        // let's check that inside database we have the same data as in response above
        /** @var User|null $user */
        $user = $this->userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $user);
        self::assertEquals(2, $user->getUserGroups()->count());

        // cleanup our actions above in order to have only 1 attached user group to the user
        /** @var UserGroup|null $userGroupForAttach */
        $userGroupForAttach = $this->userGroupResource->findOneBy([
            'role' => Role::LOGGED->value,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupForAttach);
        $user = $this->userResource->save($user->removeUserGroup($userGroupForAttach), false);
        $this->userGroupResource->save($userGroupForAttach, true, true);
        self::assertEquals(1, $user->getUserGroups()->count());
    }
}
