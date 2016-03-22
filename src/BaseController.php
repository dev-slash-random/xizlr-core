<?php
/**
 * Base Controller
 *
 * Base class for controllers. Extend this with you own controller.
 *
 * @package      Xizlr
 * @subpackage   Core
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Xizlr\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JsonSerializable;

class BaseController
{
    use Xizlr;

    /**
     * Renders any given content as a json string
     *
     * @param JsonSerializable $content  This can be serializable data type.
     * @param Response         $response The response object
     *
     * @return Response $response
     */
    public function render(JsonSerializable $content, Response $response)
    {
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }
}
