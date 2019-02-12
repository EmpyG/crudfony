<?php

namespace App\Services;

use App\Entity\Task;
use App\Exceptions\TaskNotFoundException;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private $taskRepository;
    private $entityManager;

    private const ACTIVE = 1;

    /**
     * TaskService constructor.
     *
     * @param TaskRepository         $taskRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TaskRepository $taskRepository, EntityManagerInterface $entityManager)
    {
        $this->taskRepository = $taskRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $title
     * @param string $description
     * @return Task
     * @throws \Exception
     */
    public function create(string $title, string $description)
    {
        $task = new Task();

        $task->setTitle($title)
             ->setDescription($description)
             ->setActive(self::ACTIVE);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @param int $id
     * @return Task
     * @throws TaskNotFoundException
     */
    public function read(int $id): Task
    {
        $task = $this->taskRepository->find($id);

        if (!$task instanceof Task) {
            throw new TaskNotFoundException('Task not found.');
        }

        return $task;
    }

    /**
     * @param int    $id
     * @param string $title
     * @param string $description
     * @param bool   $active
     * @return Task
     * @throws TaskNotFoundException
     */
    public function update(int $id, string $title, string $description, bool $active): Task
    {
        $task = $this->read($id);

        $task->setTitle($title)
             ->setDescription($description)
             ->setActive($active);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @param int $id
     * @return Task
     * @throws TaskNotFoundException
     */
    public function delete(int $id): Task
    {
        $task = $this->read($id);
        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return $task;
    }
}