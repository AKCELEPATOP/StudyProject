<?php
/**
 * Created by PhpStorm.
 * User: Sasha
 * Date: 30.01.2019
 * Time: 15:39
 */

namespace App\Service;


use App\Entity\Post;
use App\Entity\Task;
use App\Model\Post\ArrayOfPost;
use App\Model\Post\UserPosts;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class TaskService
{
    /**
     * @var PostRepository
     */
    private $taskRepository;

    /**
     * TaskService constructor.
     * @param PostRepository $taskRepository
     */
    public function __construct(PostRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * @param int $taskId
     * @return Post
     * @throws EntityNotFoundException
     */
    public function getTask(int $taskId): Post
    {
        $task = $this->taskRepository->find($taskId);
        if (empty($task)) {
            throw new EntityNotFoundException('Task with id ' . $taskId . ' does not exist');
        }
        return $task;
    }

    /**
     * @return array|null
     */
    public function getAllTasks(): ?array
    {
        return $this->taskRepository->findAll();
    }

    /**
     * @param Post $task
     * @return Post
     */
    public function addTask(Post $task): Post
    {
        $this->taskRepository->save($task);
    }

    /**
     * @param int $id
     * @param Post $task
     * @return Post
     * @throws EntityNotFoundException
     */
    public function updateTask(int $id, Post $task): Post
    {
        $this->getTask($id);
        $task->setId($id);
        $this->taskRepository->update($task);
    }

    /**
     * @param int $id
     * @throws EntityNotFoundException
     */
    public function deleteTask(int $id): void
    {
        $task = $this->getTask($id);
        $this->taskRepository->delete($task);
    }

    /**
     * @param int $userId
     * @return UserPosts
     */
    public function getUserTasks(int $userId): UserPosts
    {
        $posts = new ArrayOfPost(
            $this->taskRepository->findBy(['userId' => $userId], ['status' => 'ASC'])
        );
        list($average,$countRequests) = $this->getAverageSpeed($posts);
        return new UserPosts($posts,$userId,count($posts),$countRequests,$average);
    }

    /**
     * @param int $id
     * @param int $userId
     */
    public function setToProcess(int $id, int $userId) : void
    {
        $post = $this->taskRepository->find($id);

        if($post->getUser()->getId() !== $userId){
            throw new UnauthorizedHttpException("Контракт не принадлежит данному пользователю");
        }
        if($post->getStatus() > Post::NEW){
            throw new \DomainException("Контракт уже в выполнении");
        }
        $post->setStatus(Post::WAITING);
        $this->taskRepository->update($post);

        //send to queue
    }

    /**
     * @param $posts
     * @return array
     */
    private function getAverageSpeed($posts) : array
    {
        $count = 0;
        $sum = array_reduce($posts, function ($carry, Post $post) use ($count) {
            if ($post->getStatus() === Post::COMPLETED) {
                $carry += $post->getRequestDuration();
                $count++;
            }
            return $carry;
        });
        return [$sum/$count, $count];
    }
}