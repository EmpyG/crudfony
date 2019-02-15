<?php

namespace App\Controller;

use App\Exceptions\UserNotFoundException;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class UserController extends AbstractController
{
    private const EMAIL = 'email';
    private const PASS = 'password';

    private $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/user", methods={"POST"}, name="app_create_user")
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $email = $request->request->get(self::EMAIL);
        $password = $request->request->get(self::PASS);

        try {
            $user = $this->userService->create($email, $password);
            return JsonResponse::create($user);
        } catch (Throwable $e) {
            return JsonResponse::create($e->getMessage());
        }
    }

    /**
     * @Route("user/{id}", methods={"PUT"}, name="app_update_user")
     * @param Request $request
     * @param int     $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $password = $request->request->get(self::PASS);

        try {
            $user = $this->userService->update($id, $password);
            return JsonResponse::create($user);
        } catch (UserNotFoundException $e) {
            return JsonResponse::create($e->getMessage());
        }
    }

    /**
     * @Route("/user/{id}", methods={"GET"}, name="app_read_user")
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        try {
            $user = $this->userService->read($id);
            return JsonResponse::create($user);
        } catch (UserNotFoundException $e) {
            return JsonResponse::create($e->getMessage());
        }
    }

}