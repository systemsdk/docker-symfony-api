<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Auth/GetTokenController.php
 */

namespace App\Controller\Api\Auth;

use App\Utils\JSON;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetTokenController
 *
 * @OA\Tag(name="Authentication")
 *
 * @package App\Controller\Api\Auth
 */
class GetTokenController
{
    /**
     * Get user Json Web Token (JWT) for authentication.
     *
     * @Route(
     *      path="/auth/getToken",
     *      methods={"POST"},
     *  );
     *
     * @OA\Post(security={})
     *
     * @OA\RequestBody(
     *      request="body",
     *      description="Credentials object",
     *      required=true,
     *      @OA\JsonContent(
     *          type="object",
     *          example={"username": "username", "password": "password"},
     *          @OA\Property(property="username", ref=@Model(type=App\Entity\User::class, groups={"User.username"})),
     *          @OA\Property(property="password", type="string"),
     *      )
     *  )
     * @OA\Response(
     *      response=200,
     *      description="JSON Web Token for user",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"token": "_json_web_token_"},
     *          @OA\Property(property="token", type="string", description="Json Web Token"),
     *      ),
     *  )
     * @OA\Response(
     *      response=400,
     *      description="Bad Request",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"code": 400, "message": "Bad Request"},
     *          @OA\Property(property="code", type="integer", description="Error code"),
     *          @OA\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @OA\Response(
     *      response=401,
     *      description="Unauthorized",
     *      @OA\JsonContent(
     *          type="object",
     *          example={"code": 401, "message": "Bad credentials"},
     *          @OA\Property(property="code", type="integer", description="Error code"),
     *          @OA\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     *
     * @throws HttpException|JsonException
     */
    public function __invoke(): void
    {
        $message = sprintf(
            'You need to send JSON body to obtain token eg. %s',
            JSON::encode(['username' => 'username', 'password' => 'password'])
        );

        throw new HttpException(Response::HTTP_BAD_REQUEST, $message);
    }
}
