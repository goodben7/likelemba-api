<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Exception\UnavailableDataException;
use App\Exception\UnauthorizedActionException;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
    )
    {
    }

    private function findUser(string $userId): User 
    {
        $user = $this->em->find(User::class, $userId);

        if (null === $user) {
            throw new UnavailableDataException(sprintf('cannot find user with id: %s', $userId));
        }

        return $user; 
    }

    public function delete(string $userId) {
        $user = $this->findUser($userId);

        if ($user->isDeleted()) {
            throw new UnauthorizedActionException('this action is not allowed');
        }

        $user->setDeleted(true);
        $user->setUpdatedAt(new \DateTimeImmutable('now'));

        $this->em->persist($user);
        $this->em->flush();
    }    

}