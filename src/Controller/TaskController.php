<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\TaskService;
use Throwable;

class TaskController extends AbstractController
{
    private $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * @Route("/task", name="app_show")
     */
    public function index()
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    /**
     * @Route("/task", method={"POST"}, name="app_create")
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');

        try {
            $task = $this->taskService->create($title, $description);
            $this->addFlash('success', 'Succesfully added new task!');
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        } finally {
            return JsonResponse::create($this);
        }
    }

    /**
     * @Route("/task/{id}", method={"PUT"}, name="app_update")
     * @param Request $request
     * @param int     $id
     * @return JsonResponse
     * @throws \App\Exceptions\TaskNotFoundException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $active = $request->request->get('active');

        $task = $this->taskService->update($id, $title, $description, $active);

        return JsonResponse::create($task);
    }

    /**
     * @Route ("/task", method={"DELETE"}, name="app_delete")
     * @param Request $request
     * @return JsonResponse
     * @throws \App\Exceptions\TaskNotFoundException
     */
    public function delete(Request $request): JsonResponse
    {
        $id = $request->request->get('id');

        $this->taskService->delete($id);

        return JsonResponse::create($this);
    }
}
