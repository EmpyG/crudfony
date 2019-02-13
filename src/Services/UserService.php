<?php

namespace App\Services;

use App\Entity\User;
use App\Exceptions\UserNotFoundException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService
{
    private $userRepository;
    private $entityManager;

    /**
     * UserService constructor.
     *
     * @param UserRepository         $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     */
    public function create(string $email, string $password)
    {
        $user = new User();

        $user->setEmail($email)
             ->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param int $id
     * @return User|null
     * @throws UserNotFoundException
     */
    public function read(int $id)
    {
        $user = $this->userRepository->find($id);

        if (!$user instanceof User) {
            throw new UserNotFoundException('User not found.');
        }

        return $user;
    }

    /**
     * @param int    $id
     * @param string $password
     * @return User|null
     * @throws UserNotFoundException
     */
    public function update(int $id, string $password)
    {
        $user = $this->read($id);

        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param int $id
     * @return User|null
     * @throws UserNotFoundException
     */
    public function delete(int $id)
    {
        $user = $this->read($id);
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $user;
    }
}