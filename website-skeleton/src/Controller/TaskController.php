<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/task/c", name="app_create")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createTask(Request $request)
    {
        $title = $request->request->get('title');
        $description = $request->request->get('description');

        try {
            $task = $this->taskService->create($title, $description);
            $this->addFlash('success', 'Succesfully added new task!');
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        } finally {
            return $this->redirectToRoute('app_show');
        }
    }

    /**
     * @Route("/task/u", name="app_update")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateTask(Request $request)
    {
        $id = $request->request->get('id');
        $title = $request->request->get('title');
        $description = $request->request->get('description');
        $active = $request->request->get('active');

        try {
            $task = $this->taskService->update($id, $title, $description, $active);
            $this->addFlash('success', 'Succesfully updated the task!');
        } catch (Throwable $e) {
            $this->addFlash('error', $e->getMessage());
        } finally {
            return $this->redirectToRoute('app_show');
        }
    }

    public function deleteTask(Request $request)
    {
        $id = $request->request->get('id');

        $this->taskService->delete($id);

        return $this->redirectToRoute('task_show');
    }
}
