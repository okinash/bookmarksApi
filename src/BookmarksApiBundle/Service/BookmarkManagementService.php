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
use BookmarksApiBundle\Exception\BookmarkNotFoundException;
use BookmarksApiBundle\Exception\CommentPermissionsException;
use BookmarksApiBundle\Exception\WrongUrlFormatException;
use BookmarksApiBundle\Repository\CommentRepository;
use Doctrine\ORM\EntityManager;
use Negotiation\Exception\InvalidArgument;
use Psr\Log\InvalidArgumentException;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;


class BookmarkManagementService
{
    const DATETIME_OUTPUT_FORMAT = 'd.m.Y H:i:s';
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CommentRepository
     */
    private $repository;
    /**
     * @var Validator
     */
    private $validator;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $validator)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository('BookmarksApiBundle:Bookmark');
        $this->validator = $validator;
    }

    /**
     * @param string $url
     * @return array
     * @throws BookmarkNotFoundException
     */
    public function getBookmark($url)
    {
        $bookmark = $this->repository->findOneByUrl($url);
        if (!$bookmark) {
            throw new BookmarkNotFoundException();
        }
        return $this->mapBookmarkFields($bookmark);

    }

    /**
     * @param Bookmark $bookmark
     * @return array
     */
    public function mapBookmarkFields(Bookmark $bookmark)
    {
        $comments = $bookmark->getComments()->toArray();
        $comments = array_map(function (Comment $comment) {
            return [
                'id' => $comment->getId(),
                'createdAt' => $comment->getCreatedAt()->format(self::DATETIME_OUTPUT_FORMAT),
                'text' => $comment->getText(),
                'ip' => $comment->getIp()
            ];
        }, $comments);
        $response = [
            'url' => $bookmark->getUrl(),
            'id' => $bookmark->getId(),
            'createdAt' => $bookmark->getCreatedAt()->format('d.m.Y H:i:s'),
            'comments' => $comments
        ];
        return $response;
    }

    /**
     * @param string $url
     * @return Bookmark
     * @throws WrongUrlFormatException
     */
    public function createOrGetBookmark($url)
    {
        if (!$this->isUrlValid($url)) {
            throw new WrongUrlFormatException();
        }
        $bookmark = $this->repository->findOneByUrl($url);
        if (!($bookmark instanceof Bookmark)) {
            $bookmark = $this->createBookmark($url);
        }
        return $bookmark;
    }

    /**
     * @param $url
     * @return bool
     */
    public function isUrlValid($url)
    {
        $constraint = new Assert\Url();
        $errors = $this->validator->validate($url, $constraint);
        return (count($errors) == 0);

    }

    /**
     * @param $url
     * @return Bookmark
     */
    public function createBookmark($url)
    {
        $bookmark = new Bookmark();
        $bookmark->setUrl($url);
        $bookmark->setCreatedAt(new \DateTime());
        $this->entityManager->persist($bookmark);
        $this->entityManager->flush();
        return $bookmark;
    }

    /**
     * @return array
     */
    public function getLatestBookmarks()
    {
        return $this->repository->getLatest();
    }
}