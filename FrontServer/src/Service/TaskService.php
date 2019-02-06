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
use App\Entity\User;
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
        return $task;
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
        return $task;
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
     * @param Post $userId
     * @return UserPosts
     */
    public function getUserTasks(User $userId): UserPosts
    {
        $result = $this->taskRepository->findBy(['user' => $userId], ['status' => 'ASC']);
//        $result = $this->taskRepository->getUserPosts($userId);
//        var_dump($result);
        $posts = new ArrayOfPost(
            $result
        );
        list($average, $countRequests) = $this->getAverageSpeed($posts);
        return new UserPosts($posts, $userId->getId(), count($posts), $countRequests, $average);
    }

    /**
     * @param int $id
     * @param int $userId
     */
    public function setToProcess(int $id, int $userId): void
    {
        $post = $this->taskRepository->find($id);

        if ($post->getUser()->getId() !== $userId) {
            throw new UnauthorizedHttpException("Контракт не принадлежит данному пользователю");
        }
        if ($post->getStatus() > Post::STATUS_NEW) {
            throw new \DomainException("Контракт уже в выполнении");
        }
        $post->setStatus(Post::STATUS_WAITING);
        $this->taskRepository->update($post);

        //send to queue
    }

    /**
     * @param $posts
     * @return array
     */
    private function getAverageSpeed(ArrayOfPost $posts): array
    {
        $count = 0;
        $sum = array_reduce($posts->getArrayCopy(), function ($carry, Post $post) use ($count) {
            if ($post->getStatus() === Post::STATUS_COMPLETED) {
                $carry += $post->getRequestDuration();
                $count++;
            }
            return $carry;
        });
        $average = $count > 0 ? $sum / $count : 0;
        return [$average, $count];
    }
}