<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 01.02.2019
 * Time: 9:56
 */

namespace App\Model\Post;

/**
 * Class UserPosts
 * @package App\Model\Post
 */
class UserPosts
{
    /** @var ArrayOfPost  */
    private $posts;

    /** @var int  */
    private $userId;

    /** @var int  */
    private $postsCount;

    /** @var int  */
    private $requestsCount;

    /** @var float  */
    private $averageSpeed;

    /**
     * UserPosts constructor.
     * @param $posts
     * @param $userId
     * @param $postsCount
     * @param $requestsCount
     * @param $averageSpeed
     */
    public function __construct(ArrayOfPost $posts, int $userId, int $postsCount, int $requestsCount, float $averageSpeed)
    {
        $this->posts = $posts;
        $this->userId = $userId;
        $this->postsCount = $postsCount;
        $this->requestsCount = $requestsCount;
        $this->averageSpeed = $averageSpeed;
    }

    /**
     * @return ArrayOfPost
     */
    public function getPosts(): ArrayOfPost
    {
        return $this->posts;
    }

    /**
     * @param ArrayOfPost $posts
     */
    public function setPosts(ArrayOfPost $posts): void
    {
        $this->posts = $posts;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getPostsCount(): int
    {
        return $this->postsCount;
    }

    /**
     * @param int $postsCount
     */
    public function setPostsCount(int $postsCount): void
    {
        $this->postsCount = $postsCount;
    }

    /**
     * @return int
     */
    public function getRequestsCount(): int
    {
        return $this->requestsCount;
    }

    /**
     * @param int $requestsCount
     */
    public function setRequestsCount(int $requestsCount): void
    {
        $this->requestsCount = $requestsCount;
    }

    /**
     * @return float
     */
    public function getAverageSpeed(): float
    {
        return $this->averageSpeed;
    }

    /**
     * @param float $averageSpeed
     */
    public function setAverageSpeed(float $averageSpeed): void
    {
        $this->averageSpeed = $averageSpeed;
    }




}