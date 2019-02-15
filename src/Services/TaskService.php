<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\Task;
use App\Exceptions\TaskNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;

class TaskService
{
    private $taskRepository;
    private $entityManager;
    private $userService;

    private const ACTIVE = true;

    /**
     * TaskService constructor.
     *
     * @param TaskRepository         $taskRepository
     * @param UserService            $userService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        TaskRepository $taskRepository,
        UserService $userService,
        EntityManagerInterface $entityManager
    ) {
        $this->taskRepository = $taskRepository;
        $this->userService = $userService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param int    $accountId
     * @param string $title
     * @param string $description
     * @return Task
     * @throws UserNotFoundException
     */
    public function create(int $accountId, string $title, ? string $description)
    {
        $task = new Task();
        $account = $this->userService->read($accountId);

        $task->setTitle($title)
             ->setDescription($description)
             ->setActive(self::ACTIVE)
             ->setUser($account);

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

    public function readAll(): array
    {
        return $this->taskRepository->findAll();
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

    /**
     * @param int $userId
     * @return array
     * @throws UserNotFoundException
     */
    public function readRelated(int $userId): array
    {
        $user = $this->userService->read($userId);
        return $this->taskRepository->findBy(['user' => $user]);
    }

    /**
     * @param Task $task
     * @return array
     */
    public static function format(Task $task): array
    {
        return [
            'id'          => $task->getId(),
            'title'       => $task->getTitle(),
            'description' => $task->getDescription(),
            'createdAt'   => $task->getCreatedAt(),
            'updatedAt'   => $task->getUpdatedAt(),
            'active'      => $task->getActive(),
        ];
    }
}