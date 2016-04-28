<?php
/**
 * Base Controller
 *
 * Base class for controllers. Extend this with you own controller.
 *
 * @package      Mooti
 * @subpackage   Framework
 * @author       Ken Lalobo <ken@mooti.io>
 */

namespace Mooti\Framework\Rest;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JsonSerializable;

use Mooti\Framework\Framework;

class BaseController
{
    use Framework;

    /**
     * Renders any given content as a json string
     *
     * @param mixed    $content  This can be serializable data type.
     * @param Response $response The response object
     *
     * @return Response $response
     */
    public function render($content, Response $response)
    {
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($content));

        return $response;
    }
}
