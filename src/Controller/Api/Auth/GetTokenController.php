<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/Auth/GetTokenController.php
 */

namespace App\Controller\Api\Auth;

use App\Utils\JSON;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetTokenController
 *
 * @SWG\Tag(name="Authentication")
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
     * @SWG\Post(security={})
     *
     * @SWG\Parameter(
     *      name="body",
     *      in="body",
     *      description="Credentials object",
     *      required=true,
     * @SWG\Schema(
     *          example={"username": "username", "password": "password"},
     * @SWG\Property(property="username", ref=@Model(type=App\Entity\User::class, groups={"User.username"})),
     * @SWG\Property(property="password", type="string"),
     *      )
     *  )
     * @SWG\Response(
     *      response=200,
     *      description="JSON Web Token for user",
     * @SWG\Schema(
     *          type="object",
     *          example={"token": "_json_web_token_"},
     * @SWG\Property(property="token", type="string", description="Json Web Token"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=400,
     *      description="Bad Request",
     * @SWG\Schema(
     *          type="object",
     *          example={"code": 400, "message": "Bad Request"},
     * @SWG\Property(property="code", type="integer", description="Error code"),
     * @SWG\Property(property="message", type="string", description="Error description"),
     *      ),
     *  )
     * @SWG\Response(
     *      response=401,
     *      description="Unauthorized",
     * @SWG\Schema(
     *          type="object",
     *          example={"code": 401, "message": "Bad credentials"},
     * @SWG\Property(property="code", type="integer", description="Error code"),
     * @SWG\Property(property="message", type="string", description="Error description"),
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
