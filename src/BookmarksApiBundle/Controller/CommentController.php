<?php

namespace BookmarksApiBundle\Controller;

use BookmarksApiBundle\Entity\Bookmark;
use BookmarksApiBundle\Exception\CommentPermissionsException;
use BookmarksApiBundle\Service\CommentManagementService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class CommentController extends ApiController
{
    const VALIDATION_ERROR_EMPTY_TEXT = 'Empty `text` parameter. please send in format{"text":"your text"}';

    /**
     * Create comment
     * Request example {"text":"test"}
     * Response example {"id":1}
     * @ApiDoc()
     * @param Request $request
     * @param int $bookmarkId id of bookmark
     * @return JsonResponse
     */
    public function newAction(Request $request, $bookmarkId)
    {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['text'])) {
            return $this->createErrorResponse(self::VALIDATION_ERROR_EMPTY_TEXT, Response::HTTP_BAD_REQUEST);
        }
        $text = $content['text'];
        $bookmark = $this->getBookmarkRepository()->find($bookmarkId);
        if (!($bookmark instanceof Bookmark)) {
            return $this->createErrorResponse('Bookmark not found', Response::HTTP_NOT_FOUND);
        }
        /**
         * @var CommentManagementService $service
         */
        $service = $this->get('comment.management.service');
        $comment = $service->createComment($bookmark, $text, $request->getClientIp());
        return $this->createResponse(array('id' => $comment->getId()));
    }

    /**
     * Update Comment
     * @ApiDoc()
     * @param Request $request
     * @param int $commentId ID of comment
     * @return JsonResponse
     */
    public function updateAction(Request $request, $commentId)
    {
        $content = json_decode($request->getContent(), true);
        if (!isset($content['text'])) {
            return $this->createErrorResponse(self::VALIDATION_ERROR_EMPTY_TEXT, Response::HTTP_BAD_REQUEST);
        }
        $text = $content['text'];
        /**
         * @var CommentManagementService $service
         */
        $service = $this->get('comment.management.service');
        try {
            $service->updateComment((int)$commentId, $request->getClientIp(), $text);
        } catch (CommentPermissionsException $exception) {
            return $this->createResponse(['error' => $exception->getMessage()], $exception->getCode());
        }
        return $this->createResponse([], Response::HTTP_OK);
    }

    /**
     * Delete comment
     * @ApiDoc()
     * @param Request $request
     * @param int $commentId ID of comment
     * @return JsonResponse
     */
    public function deleteAction(Request $request, $commentId)
    {
        /**
         * @var CommentManagementService $service
         */
        $service = $this->get('comment.management.service');
        try {
            $service->deleteComment((int)$commentId, $request->getClientIp());
        } catch (CommentPermissionsException $exception) {
            return $this->createResponse(['error' => $exception->getMessage()], $exception->getCode());
        }
        return $this->createResponse([], Response::HTTP_NO_CONTENT);
    }
}
