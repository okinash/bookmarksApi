<?php

namespace BookmarksApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    protected function createResponse(array $data, $code = Response::HTTP_OK)
    {
        $response = new JsonResponse($data, $code);
        $response->headers->add([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'POST,GET,PUT,DELETE'
        ]);
        return $response;
    }

    protected function createErrorResponse($error = '', $code = Response::HTTP_NOT_FOUND)
    {
        return $this->createResponse(['error' => $error], $code);
    }

    protected function getBookmarkRepository()
    {
        return $this->getDoctrine()->getManager()->getRepository('BookmarksApiBundle:Bookmark');
    }
}
