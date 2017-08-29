<?php

namespace BackBundle\Entity;

/**
 * Comment
 */
class Comment
{
    /**
     * @var integer
     */
    private $commentid;

    /**
     * @var string
     */
    private $body;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updateAt;

    /**
     * @var \BackBundle\Entity\User
     */
    private $userid;

    /**
     * @var \BackBundle\Entity\Video
     */
    private $videoid;


    /**
     * Get commentid
     *
     * @return integer
     */
    public function getCommentid()
    {
        return $this->commentid;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Comment
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
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
     * Set updateAt
     *
     * @param \DateTime $updateAt
     *
     * @return Comment
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }

    /**
     * Set userid
     *
     * @param \BackBundle\Entity\User $userid
     *
     * @return Comment
     */
    public function setUserid(\BackBundle\Entity\User $userid = null)
    {
        $this->userid = $userid;

        return $this;
    }

    /**
     * Get userid
     *
     * @return \BackBundle\Entity\User
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * Set videoid
     *
     * @param \BackBundle\Entity\Video $videoid
     *
     * @return Comment
     */
    public function setVideoid(\BackBundle\Entity\Video $videoid = null)
    {
        $this->videoid = $videoid;

        return $this;
    }

    /**
     * Get videoid
     *
     * @return \BackBundle\Entity\Video
     */
    public function getVideoid()
    {
        return $this->videoid;
    }
}

