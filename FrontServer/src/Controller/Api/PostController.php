<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Repository\UserRepository;
use App\Service\TaskService;
use App\Service\Validate;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/post")
 * Class PostController
 * @package App\Controller\Api
 */
class PostController extends AbstractController
{
    /** @var TaskService */
    private $service;

    /** @var NormalizerInterface */
    private $normalizer;

    /** @var SerializerInterface */
    private $serializer;

    /** @var UserRepository */
    private $userRepository;

    public function __construct(TaskService $service, NormalizerInterface $normalizer,
                                SerializerInterface $serializer,
                                UserRepository $userRepository)
    {
        $this->service = $service;
        $this->normalizer = $normalizer;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/{id}", name="post_show", methods={"GET"})
     */
    public function show(int $id)
    {
        try {
            $task = $this->service->getTask($id);
        } catch (EntityNotFoundException $ex) {
            $response = array(
                'code' => 1,
                'message' => 'post not found',
                'error' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->get('serializer')->serialize($task, 'json');

        $response = array(
            'code' => 0,
            'message' => 'success',
            'errors' => null,
            'result' => json_decode($data)
        );
        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/",name="post_new", methods={"POST"})
     * @param Request $request
     * @param Validate $validate
     * @return JsonResponse
     */
    public function new(Request $request, Validate $validate)
    {
        $data = $request->getContent();
        $post = $this->get('serializer')->deserialize($data, 'App\Entity\Post', 'json');

        $reponse = $validate->validateRequest($post);
        if (!empty($reponse)) {
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }

        $userId = $this->getUserId();

        $post->setUser($this->userRepository->find($userId));

        $this->service->addTask($post);

        $response = array(
            'code' => 0,
            'message' => 'Post created!',
            'errors' => null,
            'result' => null
        );
        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * @Route("/", name="post_index", methods={"GET"})
     * @return JsonResponse
     */
    public function index()
    {
        $posts = $this->service->getAllTasks();

        if (!count($posts)) {
            $response = array(
                'code' => 1,
                'message' => 'No posts found!',
                'errors' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->normalizer->normalize($posts, 'json', ['groups' => Post::GROUP]);
        $response = array(
            'code' => 0,
            'message' => 'success',
            'errors' => null,
            'result' => $data
        );
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @Route("/{id}", name="update_post", methods={"PUT"})
     * @param Request $request
     * @param $id
     * @param Validate $validate
     * @return JsonResponse
     */
    public function edit(Request $request, $id, Validate $validate)
    {
        $body = $request->getContent();
        $data = $this->serializer->deserialize($body, 'App\Entity\Post', 'json', ['groups' => Post::GROUP]);

        $reponse = $validate->validateRequest($data);

        if (!empty($reponse)) {
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->service->updateTask($id, $data);
            $response = array(
                'code' => 0,
                'message' => 'Post updated!',
                'errors' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $ex) {
            return new JsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * $Route("/{id}, name="post_delete", methods={"DELETE"})
     * @param $id
     * @return JsonResponse
     */
    public function delete($id)
    {
        try {
            $this->service->deleteTask($id);
        } catch (EntityNotFoundException $ex) {
            $response = array(
                'code' => 1,
                'message' => 'Post not found',
                'errors' => null,
                'result' => null
            );
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        } catch (\Exception $ex) {
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/getUserPost", name="get_user_posts", methods={"GET"})
     * @return JsonResponse
     */
    public function getUserPosts()
    {
        $userId = $this->getUserId();
        try {
            $result = $this->service->getUserTasks($userId);
            return new JsonResponse($result, Response::HTTP_OK);
        } catch (\Exception $ex) {
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/setToProcess", name="set_to_process", methods={"PUT"})
     * @param $id
     * @return JsonResponse
     */
    public function setToProcess($id)
    {
        $userId = $this->getUserId();
        try{
            $this->service->setToProcess($id, $userId);
            $response = array(
                'code' => 0,
                'message' => 'Post sent to process',
            );
            return new JsonResponse($response, Response::HTTP_OK);
        }catch (UnauthorizedHttpException $ex){
            $response = array(
                'code' => 1,
                'message' => $ex->getMessage(),
            );
            return new JsonResponse($response, Response::HTTP_UNAUTHORIZED);
        }catch (\DomainException $ex){
            $response = array(
                'code' => 1,
                'message' => $ex->getMessage(),
            );
            return new JsonResponse($response, Response::HTTP_METHOD_NOT_ALLOWED);
        }catch (\Exception $ex){
            $response = array(
                'code' => 1,
                'message' => 'Something broken try again later',
            );
            return new JsonResponse($response, Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @return int
     */
    private function getUserId() : int
    {
        return $this->getUser()->getId();
    }
}
