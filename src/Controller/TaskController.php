<?php declare(strict_types=1);

namespace App\Controller;

use App\Exceptions\TaskNotFoundException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TaskService;
use Throwable;

class TaskController extends AbstractController
{
    private const TITLE = 'title';
    private const DESCRIPTION = 'description';
    private const ACTIVE = 'active';

    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * @Route("/task", methods={"POST"}, name="app_create_task")
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $title = $request->request->get(self::TITLE);
        $description = $request->request->get(self::DESCRIPTION);
        $accountId = (int)$request->request->get('account');

        try {
            $task = $this->taskService->create($accountId, $title, $description);
            return JsonResponse::create($this->taskService::format($task));
        } catch (Throwable $e) {
            return JsonResponse::create($e->getMessage());
        }
    }

    /**
     * @Route("/task/{id}", methods={"PUT"}, name="app_update_task")
     * @param Request $request
     * @param int     $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $title = $request->request->get(self::TITLE);
        $description = $request->request->get(self::DESCRIPTION);
        $active = $request->request->get(self::ACTIVE);

        try {
            $task = $this->taskService->update($id, $title, $description, $active);
            return JsonResponse::create($task);
        } catch (TaskNotFoundException $e) {
            return JsonResponse::create($e->getMessage());
        }
    }

    /**
     * @Route("/task/{id}", methods={"GET"}, name="app_read_task")
     * @param int $id
     * @return JsonResponse
     */
    public function read(int $id): JsonResponse
    {
        try {
            $task = $this->taskService->read($id);
            return JsonResponse::create($this->taskService::format($task));
        } catch (TaskNotFoundException $e) {
            return JsonResponse::create($e->getMessage());
        }
    }

    /**
     * @Route("/task", methods={"GET"}, name="app_read_tasks")
     * @return JsonResponse
     */
    public function readAll(): JsonResponse
    {
        $tasks = $this->taskService->readAll();
        $response = [];
        foreach ($tasks as $each) {
            $response[] = $this->taskService::format($each);
        }

        return JsonResponse::create($response);
    }

    /**
     * @Route ("/task/{id}", methods={"DELETE"}, name="app_delete_task")
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            $this->taskService->delete($id);
            return JsonResponse::create([], 204);
        } catch (TaskNotFoundException $e) {
            return JsonResponse::create($e->getMessage());
        }

    }

    /**
     * @Route("/tasks/{userId}", methods={"GET"}, name="app_user_tasks")
     * @param int $userId
     * @return JsonResponse
     * @throws \App\Exceptions\UserNotFoundException
     */
    public function readUserTasks(int $userId): JsonResponse
    {
        $tasks = $this->taskService->readRelated($userId);
        $response = [];
        foreach ($tasks as $each) {
            $response[] = $this->taskService::format($each);
        }

        return JsonResponse::create($response);
    }
}
