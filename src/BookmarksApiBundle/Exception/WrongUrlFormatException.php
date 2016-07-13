<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 14.07.2016
 * Time: 1:03
 */

namespace BookmarksApiBundle\Exception;


use Symfony\Component\HttpFoundation\Response;

class WrongUrlFormatException extends \Exception
{
    const VALIDATION_ERROR_WRONG_URL_FORMAT = 'Wrong url format';

    public function __construct()
    {
        return parent::__construct(self::VALIDATION_ERROR_WRONG_URL_FORMAT, Response::HTTP_BAD_REQUEST);
    }
}