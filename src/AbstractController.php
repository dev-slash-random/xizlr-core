<?php
/*
 *
 * @author Ken Lalobo
 *
 */

namespace Mooti\Xizlr\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController
{
	abstract public function getAll(Request $request, Response $response);

	public function render(array $content, Response $response)
	{
		$response->setStatusCode(Response::HTTP_OK);
		$response->headers->set('Content-Type', 'text/json');
		$response->setContent(json_encode($content));

		return $response;
	}
}
