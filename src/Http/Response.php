<?php
/*
 * Response class
 *
 */
namespace Mooti\Xizlr\Core\Http;

class Response
{
    private $content = 'PONG';

    public function getContent()
    {
        return $this->content;
    }
}
