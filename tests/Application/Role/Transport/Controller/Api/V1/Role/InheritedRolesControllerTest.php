<?php

declare(strict_types=1);

namespace App\Tests\Application\Role\Transport\Controller\Api\V1\Role;

use App\General\Domain\Utils\JSON;
use App\Role\Application\Security\Interfaces\RolesServiceInterface;
use App\Role\Domain\Enum\Role;
use App\Tests\TestCase\WebTestCase;
use Exception;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * @package App\Tests
 */
class InheritedRolesControllerTest extends WebTestCase
{
    protected static string $baseUrl = self::API_URL_PREFIX . '/v1/role';
    private readonly RolesServiceInterface $rolesService;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->rolesService = static::getContainer()->get(RolesServiceInterface::class);
    }

    /**
     * @throws Throwable
     */
    #[TestDox('Test that `GET /api/v1/role/{role}/inherited` request returns `401` for non-logged user.')]
    public function testThatGetBaseRouteReturn401(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', static::$baseUrl . '/' . Role::LOGGED->value . '/inherited');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode(), "Response:\n" . $response);
    }

    /**
     * @throws Throwable
     */
    #[DataProvider('dataProviderTestThatActionWorksAsExpected')]
    #[TestDox('Test that `GET /api/v1/role/{role}/inherited` returns `$responseCode` with login: `$login`, '
        . 'password: `$password`.')]
    public function testThatFindActionWorksAsExpected(string $login, string $password, int $responseCode): void
    {
        $client = $this->getTestClient($login, $password);

        $client->request('GET', static::$baseUrl . '/' . Role::ROOT->value . '/inherited');
        $response = $client->getResponse();
        $content = $response->getContent();
        self::assertNotFalse($content);
        self::assertSame($responseCode, $response->getStatusCode(), "Response:\n" . $response);

        if ($responseCode === Response::HTTP_OK) {
            $responseData = JSON::decode($content, true);
            self::assertIsArray($responseData);
            $this->checkResponse($responseData);
        }
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: int}>
     */
    public static function dataProviderTestThatActionWorksAsExpected(): Generator
    {
        // username === login
        yield ['john', 'password', Response::HTTP_FORBIDDEN];
        yield ['john-logged', 'password-logged', Response::HTTP_FORBIDDEN];
        yield ['john-api', 'password-api', Response::HTTP_FORBIDDEN];
        yield ['john-user', 'password-user', Response::HTTP_FORBIDDEN];
        yield ['john-admin', 'password-admin', Response::HTTP_OK];
        yield ['john-root', 'password-root', Response::HTTP_OK];
    }

    /**
     * @param array<int, string> $responseData
     */
    private function checkResponse(array $responseData): void
    {
        self::assertEquals($this->rolesService->getInheritedRoles([Role::ROOT->value]), $responseData);
    }
}
