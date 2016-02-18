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

class BaseController
{
    use Xizlr;

    /**
     * Renders any given content. It uses the current serializer to turn the data into json.
     *
     * @param mixed content This can be serializable data type.
     *
     * @return Response $response
     */
    public function render($content, Response $response)
    {
        $serializer = $this->getService(Services::SERIALIZER);

        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($serializer->serialize($content, 'json'));

        return $response;
    }
}
