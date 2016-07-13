<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 13.07.2016
 * Time: 22:14
 */

namespace BookmarksApiBundle\Service;


use BookmarksApiBundle\Entity\Bookmark;
use BookmarksApiBundle\Entity\Comment;
use BookmarksApiBundle\Exception\CommentPermissionsException;
use BookmarksApiBundle\Repository\CommentRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class CommentManagementService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CommentRepository
     */
    private $repository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository('BookmarksApiBundle:Comment');
    }

    /**
     * @param integer $commentId
     * @param string $ip
     * @throws CommentPermissionsException
     */
    public function deleteComment($commentId, $ip)
    {
        $comment = $this->repository->find($commentId);
        if (!($comment instanceof Comment)) {
            throw new CommentPermissionsException(CommentPermissionsException::MESSAGE_NOT_FOUND,
                Response::HTTP_NOT_FOUND);
        }
        if (!$comment->permissionsGranted($ip)) {
            throw new CommentPermissionsException(CommentPermissionsException::MESSAGE_NO_ACCESS);
        }
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
    }

    /**
     * @param int $commentId
     * @param string $ip
     * @param string $text
     * @throws CommentPermissionsException
     */
    public function updateComment($commentId, $ip, $text)
    {
        $comment = $this->repository->find($commentId);
        if (!($comment instanceof Comment)) {
            throw new CommentPermissionsException(CommentPermissionsException::MESSAGE_NOT_FOUND,
                Response::HTTP_NOT_FOUND);
        }
        if (!$comment->permissionsGranted($ip)) {
            throw new CommentPermissionsException(CommentPermissionsException::MESSAGE_NO_ACCESS);
        }
        $comment->setText($text);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
    }

    /**
     * @param Bookmark $bookmark
     * @param string $text
     * @param string $ip
     * @return Comment
     */
    public function createComment(Bookmark $bookmark, $text, $ip)
    {
        $comment = new Comment();
        $comment->setText($text);
        $comment->setIp($ip);
        $comment->setCreatedAt(new \DateTime());
        $comment->setBookmark($bookmark);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
        return $comment;
    }


}