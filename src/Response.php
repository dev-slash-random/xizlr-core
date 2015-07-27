<?php
/*
 * Response class
 *
 */
namespace Mooti\Xizlr\Core;

class Response
{
    private $content = 'PONG';

    public function getContent()
    {
        return $this->content;
    }
}
