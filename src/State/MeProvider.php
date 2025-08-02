<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class MeProvider implements ProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private LoggerInterface $logger
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $token = $this->tokenStorage->getToken();
        
        if (!$token) {
            $this->logger->warning('No token found when accessing users/about endpoint');
            throw new AccessDeniedException('Not authenticated');
        }
        
        $user = $token->getUser();
        
        if (!$user instanceof User) {
            $this->logger->warning('Token does not contain a valid User object', [
                'user_type' => get_class($user)
            ]);
            throw new AccessDeniedException('Invalid user in token');
        }
        
        $this->logger->info('User successfully authenticated and accessed users/about endpoint', [
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
        
        return $user;
    }
}