<?php

namespace BookmarksApiBundle\Controller;

use BookmarksApiBundle\Exception\BookmarkNotFoundException;
use BookmarksApiBundle\Exception\WrongUrlFormatException;
use BookmarksApiBundle\Service\BookmarkManagementService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BookmarksController extends ApiController
{
    const VALIDATION_ERROR_URL_IS_NOT_SET = 'Empty `url` parameter. please send in format{"url":"http://yourlink.com"}';

    const MESSAGE_BOOKMARK_NOT_FOUND = 'Bookmark not found';

    /**
     * Get bookmark by url.
     * Response example: {"url":"https:\/\/vk.com","id":8,"createdAt":"14.07.2016 00:10:25","comments":[{"id":11,"createdAt":"14.07.2016 01:06:43","text":"not bad","ip":"127.0.0.1"}]}
     * @ApiDoc()
     * @param string $url
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAction($url)
    {
        try {
            $data = $this->getBookmarkManagementService()->getBookmark($url);
        } catch (BookmarkNotFoundException $exception) {
            return $this->createErrorResponse(self::MESSAGE_BOOKMARK_NOT_FOUND, Response::HTTP_NOT_FOUND);
        }
        return $this->createResponse($data);
    }

    /**
     * Create bookmark.
     * Request example: {"url":"http://sdd.dd"} .
     * Response example {"id":1}
     * @ApiDoc()
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newAction(Request $request)
    {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['url'])) {
            return $this->createErrorResponse(self::VALIDATION_ERROR_URL_IS_NOT_SET, Response::HTTP_BAD_REQUEST);
        }
        $url = $content['url'];
        try {
            $bookmark = $this->getBookmarkManagementService()->createOrGetBookmark($url);
        } catch (WrongUrlFormatException $exception) {
            return $this->createErrorResponse($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        }
        return $this->createResponse(array('id' => $bookmark->getId()));
    }

    /**
     * Get latest bookmarks.
     * Response example : [{"url":"http://vk.com"},{"url":"http://google.com"}]
     * @ApiDoc()
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function latestAction()
    {
        $data = $this->getBookmarkManagementService()->getLatestBookmarks();
        return $this->createResponse($data);
    }

    /**
     * @return BookmarkManagementService
     */
    private function getBookmarkManagementService()
    {
        return $this->get('bookmark.management.service');
    }


}
