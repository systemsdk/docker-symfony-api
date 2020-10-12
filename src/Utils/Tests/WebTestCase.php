<?php
declare(strict_types = 1);
/**
 * /src/Utils/Tests/WebTestCase.php
 */

namespace App\Utils\Tests;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Throwable;

/**
 * Class WebTestCase
 *
 * @package App\Tests
 */
abstract class WebTestCase extends BaseWebTestCase
{
    public const API_URL_PREFIX = '/api';
    private Auth $authService;

    /**
     * @codeCoverageIgnore
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        gc_enable();
    }

    /**
     * @codeCoverageIgnore
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        gc_collect_cycles();
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        /** @var Auth $authService */
        $authService = self::$container->get('test.app.utils.tests.auth');
        $this->authService = $authService;
    }

    /**
     * Helper method to get authorized client for specified username and password.
     *
     * @param array<mixed>|null $options
     * @param array<mixed>|null $server
     *
     * @throws Throwable
     */
    public function getTestClient(
        ?string $username = null,
        ?string $password = null,
        ?array $options = null,
        ?array $server = null
    ): KernelBrowser {
        $options ??= [];
        $server ??= [];
        // Merge authorization headers
        $server = array_merge(
            $username === null || $password === null
                ? []
                : $this->authService->getAuthorizationHeadersForUser($username, $password),
            $this->getJsonHeaders(),
            $this->authService->getJwtHeaders(),
            $server
        );

        self::ensureKernelShutdown();

        return static::createClient(array_merge($options, ['debug' => false]), $server);
    }

    /**
     * Helper method to get authorized API Key client for specified role.
     *
     * @param array<mixed>|null $options
     * @param array<mixed>|null $server
     */
    public function getApiKeyClient(?string $role = null, ?array $options = null, ?array $server = null): KernelBrowser
    {
        $options ??= [];
        $server ??= [];
        // Merge authorization headers
        $server = array_merge(
            $role === null ? [] : $this->authService->getAuthorizationHeadersForApiKey($role),
            $this->getJsonHeaders(),
            $this->authService->getJwtHeaders(),
            $server
        );

        self::ensureKernelShutdown();

        return static::createClient($options, $server);
    }

    /**
     * @return array<string, string>
     */
    public function getJsonHeaders(): array
    {
        return [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_X-Requested-With' => 'XMLHttpRequest',
        ];
    }
}
