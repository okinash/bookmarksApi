<?php
namespace BookmarksApiBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

class CommentPermissionsException extends \Exception
{
    const MESSAGE_NOT_FOUND = "Comment not found";
    const MESSAGE_NO_ACCESS  = "Your IP address has changed or your left comment more than 1 hour ago";

    public function __construct($message, $code = Response::HTTP_FORBIDDEN)
    {
        parent::__construct($message, $code);
    }
}