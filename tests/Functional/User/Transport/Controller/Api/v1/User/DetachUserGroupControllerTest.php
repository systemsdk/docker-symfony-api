<?php

declare(strict_types=1);

namespace App\Tests\Functional\User\Transport\Controller\Api\v1\User;

use App\General\Domain\Utils\JSON;
use App\General\Transport\Utils\Tests\WebTestCase;
use App\Role\Domain\Entity\Role;
use App\User\Application\Resource\UserGroupResource;
use App\User\Application\Resource\UserResource;
use App\User\Domain\Entity\User;
use App\User\Domain\Entity\UserGroup;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Class DetachUserGroupControllerTest
 *
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

        $userResource = static::getContainer()->get(UserResource::class);
        $userGroupResource = static::getContainer()->get(UserGroupResource::class);
        self::assertInstanceOf(UserResource::class, $userResource);
        self::assertInstanceOf(UserGroupResource::class, $userGroupResource);
        $this->userResource = $userResource;
        $user = $this->userResource->findOneBy([
            'username' => 'john-user',
        ]);
        self::assertInstanceOf(User::class, $user);
        $this->user = $user;
        // let's attach role to the user in order to detach it in the test bellow
        $userGroupForAttachAndDetach = $userGroupResource->findOneBy([
            'role' => Role::ROLE_LOGGED,
        ]);
        self::assertInstanceOf(UserGroup::class, $userGroupForAttachAndDetach);
        $this->userGroupForAttachAndDetach = $userGroupForAttachAndDetach;
        $this->user = $this->userResource->save($this->user->addUserGroup($this->userGroupForAttachAndDetach), false);
        $userGroupResource->save($this->userGroupForAttachAndDetach, true, true);
        self::assertEquals(2, $this->user->getUserGroups()->count());
    }

    /**
     * @testdox Test that `DELETE /api/v1/user/{userId}/group/{groupId}` under the non-root user returns error response.
     *
     * @throws Throwable
     */
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
     * @testdox Test that `DELETE /api/v1/user/{userId}/group/{groupId}` under the root user returns success response.
     *
     * @throws Throwable
     */
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
            self::assertContains($userGroup['role']['id'], [Role::ROLE_USER]);
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
        self::assertEquals(Role::ROLE_USER, $userGroup->getRole()->getId());
    }
}
