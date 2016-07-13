<?php
namespace BookmarksApiBundle\Exception;

use Symfony\Component\HttpFoundation\Response;

class BookmarkNotFoundException extends \Exception
{
    const MESSAGE_NOT_FOUND = "Bookmark not found";

    public function __construct()
    {
        parent::__construct(self::MESSAGE_NOT_FOUND, Response::HTTP_NOT_FOUND);
    }
}