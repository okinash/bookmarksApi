<?php

namespace BookmarksApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity()
 */
class Comment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=2000)
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="BookmarksApiBundle\Entity\Bookmark", inversedBy="comments")
     * @ORM\JoinColumn(name="bookmark_id",nullable=false)
     * @var Bookmark
     */
    private $bookmark;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comment
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Comment
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }


    /**
     * @return Bookmark
     */
    public function getBookmark()
    {
        return $this->bookmark;
    }

    /**
     * @param Bookmark $bookmark
     * @return Comment
     */
    public function setBookmark(Bookmark $bookmark)
    {
        $this->bookmark = $bookmark;
        return $this;
    }

    public function permissionsGranted($ip)
    {
        $currentDateTime = new \DateTime();
        $currentDateTime->sub(new \DateInterval('PT1H'));
        if ($this->createdAt < $currentDateTime || $this->ip != $ip) {
            return false;
        }
        return true;
    }

}

