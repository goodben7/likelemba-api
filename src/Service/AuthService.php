<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\AuthSession;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AuthSessionRepository;

class AuthService
{
    public function __construct(
        private EntityManagerInterface $em,
        private AuthSessionRepository $authSessionRepo,
        private UserRepository $userRepo,
        //private SmsService $smsService,
    ) {}

    public function sendOtp(string $phone): void
    {
        $otp = (string) random_int(100000, 999999);
        $session = new AuthSession();
        $session->setPhone($phone);
        $session->setOtpCode($otp);
        $session->setCreatedAt(new \DateTimeImmutable());
        $session->setExpiresAt((new \DateTimeImmutable())->modify('+5 minutes'));
        $session->setIsValidated(false);

        $this->em->persist($session);
        $this->em->flush();

        //$this->smsService->send($phone, "Votre code Likelemba est : $otp");
    }

    public function verifyOtp(string $phone, string $code): ?User
    {
        $session = $this->authSessionRepo->findValidSession($phone, $code);

        if (!$session) return null;

        $session->setIsValidated(true);
        $user = $this->userRepo->findOneBy(['phone' => $phone]);

        if (!$user) {
            $user = new User();
            $user->setPhone($phone);
            $user->setUsername($phone); // Utiliser le numéro de téléphone comme nom d'utilisateur par défaut
            $user->setPassword(null); // Définir le mot de passe à null pour les utilisateurs authentifiés par OTP
            $user->setDeleted(false);
            $user->setIsValidated(true);
            $user->setCreatedAt(new \DateTimeImmutable());
            $this->em->persist($user);
        }

        $this->em->flush();
        return $user;
    }
    
    /**
     * Get the latest OTP session for a phone number
     * Used for testing purposes
     */
    public function getLatestSession(string $phone): ?AuthSession
    {
        return $this->authSessionRepo->findOneBy(
            ['phone' => $phone, 'isValidated' => false],
            ['createdAt' => 'DESC']
        );
    }
}
