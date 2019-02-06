<?php

namespace App\Controller\Api;

use App\Entity\Post;
use App\Repository\UserRepository;
use App\Service\TaskService;
use App\Service\Validate;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class PostController
 * @package App\Controller\Api
 */
class PostController extends AbstractFOSRestController
{
    /** @var TaskService */
    private $service;

    /** @var NormalizerInterface */
    private $normalizer;

    /** @var SerializerInterface */
    private $serializer;

    /** @var UserRepository */
    private $userRepository;

    /** @var ValidatorInterface */
    private $validate;

    public function __construct(TaskService $service, NormalizerInterface $normalizer,
                                SerializerInterface $serializer,
                                UserRepository $userRepository,
                                ValidatorInterface $validate)
    {
        $this->service = $service;
        $this->normalizer = $normalizer;
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->validate = $validate;
    }

//    /**
//     * @Rest\Get("/api/post/{id}")
//     * @param int $id
//     * @return JsonResponse
//     */
//    public function show(int $id)
//    {
//        try {
//            $task = $this->service->getTask($id);
//        } catch (EntityNotFoundException $ex) {
//            $response = array(
//                'code' => 1,
//                'message' => 'post not found',
//                'error' => null,
//                'result' => null
//            );
//            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
//        }
//
//        $data = $this->get('serializer')->serialize($task, 'json');
//
//        $response = array(
//            'code' => 0,
//            'message' => 'success',
//            'errors' => null,
//            'result' => json_decode($data)
//        );
//        return new JsonResponse($response, 200);
//    }

    /**
     * @Rest\Post("/api/post")
     */
    public function new(Request $request)
    {
        $data = $request->getContent();
        $post = $this->get('serializer')->deserialize($data, 'App\Entity\Post', 'json');

        $errors = $this->validate->validate($post);
        if (count($errors) > 0) {
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST);
        }

        $userId = $this->getUserId();

        $post->setUser($this->userRepository->find($userId));
        $post->setStatus(Post::STATUS_NEW);

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
     * @Rest\Get("/api/post/")
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

//        $serializer = new Serializer([new DateTimeNormalizer(),$this->normalizer], array('json' => new JsonEncoder()));

        $data = $this->serializer->serialize($posts, 'json', ['groups' => Post::GROUP_POST]);
        $response = array(
            'code' => 0,
            'message' => 'success',
            'errors' => null,
            'result' => json_decode($data)
        );
        return new JsonResponse($response, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/api/post/{id}")
     * @param Request $request
     * @param $id
     * @param Validate $validate
     * @return JsonResponse
     */
    public function edit(Request $request, int $id)
    {
        $body = $request->getContent();
        $data = $this->serializer->deserialize($body, 'App\Entity\Post', 'json', ['groups' => Post::GROUP_POST]);

        $reponse = $this->validate->validate($data);

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
     * @Rest\Delete("/api/post/{id}")
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
     * @Rest\Get("/api/post/getUserPost/", name="get_user_posts")
     * @return JsonResponse
     */
    public function getUserPosts()
    {
//        $userId = $this->getUserId();
        $userId = 1;
        try {
            $result = $this->service->getUserTasks($this->userRepository->find($userId));
            $result = $this->serializer->serialize($result, 'json', ['groups' => Post::GROUP_POST]);
            $response = array(
                'code' => 0,
                'message' => null,
                'errors' => null,
                'result' => json_decode($result)
            );
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (\Exception $ex) {
            return new JsonResponse($ex->getMessage() . $ex->getTraceAsString(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Rest\Post("/api/post/setToProcess/{id}", name="set_to_process")
     * @param $id
     * @return JsonResponse
     */
    public function setToProcess($id)
    {
        $userId = 1; //$this->getUserId();
        try {
            $this->service->setToProcess($id, $userId);
            $response = array(
                'code' => 0,
                'message' => 'Post sent to process',
            );
            return new JsonResponse($response, Response::HTTP_OK);
        } catch (UnauthorizedHttpException $ex) {
            $response = array(
                'code' => 1,
                'message' => $ex->getMessage(),
            );
            return new JsonResponse($response, Response::HTTP_UNAUTHORIZED);
        } catch (\DomainException $ex) {
            $response = array(
                'code' => 1,
                'message' => $ex->getMessage(),
            );
            return new JsonResponse($response, Response::HTTP_METHOD_NOT_ALLOWED);
        } catch (\Exception $ex) {
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
    private function getUserId(): int
    {
        return $this->getUser()->getId();
    }
}
