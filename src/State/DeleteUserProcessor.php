<?php

namespace App\State;

use App\Manager\UserManager;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DeleteUserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserManager $manager,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        private EventDispatcherInterface $eventDispatcher
    ) {   
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // Supprimer l'utilisateur
        $this->manager->delete($uriVariables['id']);
        
        // Déconnecter l'utilisateur
        $request = $this->requestStack->getCurrentRequest();
        
        // Dispatch logout event pour nettoyer correctement la session
        $this->eventDispatcher->dispatch(new LogoutEvent($request, $this->tokenStorage->getToken()));
        
        // Effacer le token
        $this->tokenStorage->setToken(null);
        
        // Invalider la session
        $session = $request->getSession();
        if ($session) {
            $session->invalidate();
        }
    }
}