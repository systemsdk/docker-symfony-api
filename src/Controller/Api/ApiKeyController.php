<?php
declare(strict_types = 1);
/**
 * /src/Controller/Api/ApiKeyController.php
 */

namespace App\Controller\Api;

use App\DTO\ApiKey\ApiKeyCreate;
use App\DTO\ApiKey\ApiKeyPatch;
use App\DTO\ApiKey\ApiKeyUpdate;
use App\Resource\ApiKeyResource;
use App\Rest\Controller;
use App\Rest\ResponseHandler;
use App\Rest\Traits\Actions;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ApiKeyController
 *
 * @Route(
 *     path="/api_key",
 *  )
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 *
 * @SWG\Tag(name="ApiKey Management")
 *
 * @package App\Controller\Api
 *
 * @method ApiKeyResource getResource()
 * @method ResponseHandler getResponseHandler()
 */
class ApiKeyController extends Controller
{
    // Traits for REST actions
    use Actions\Root\CountAction;
    use Actions\Root\FindAction;
    use Actions\Root\FindOneAction;
    use Actions\Root\IdsAction;
    use Actions\Root\CreateAction;
    use Actions\Root\DeleteAction;
    use Actions\Root\PatchAction;
    use Actions\Root\UpdateAction;

    /**
     * @var array<string, string>
     */
    protected static array $dtoClasses = [
        Controller::METHOD_CREATE => ApiKeyCreate::class,
        Controller::METHOD_UPDATE => ApiKeyUpdate::class,
        Controller::METHOD_PATCH => ApiKeyPatch::class,
    ];

    /**
     * Constructor
     *
     * @param ApiKeyResource $resource
     */
    public function __construct(ApiKeyResource $resource)
    {
        $this->setResource($resource);
    }
}
