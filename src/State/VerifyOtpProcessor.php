<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Dto\OtpRequest;
use App\Service\AuthService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class VerifyOtpProcessor implements ProcessorInterface
{
    public function __construct(
        private AuthService $authService,
        private TokenStorageInterface $tokenStorage
    ) {}

    /**
     * @param OtpRequest $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $phone = $data->getPhone();
        $code = $data->getCode();

        if (!$phone || !$code) {
            throw new BadRequestHttpException('Phone number and OTP code are required');
        }

        $user = $this->authService->verifyOtp($phone, $code);
        
        if (!$user) {
            throw new UnauthorizedHttpException('', 'Invalid OTP code');
        }

        // L'authentification réelle est gérée par l'OtpAuthenticator
        // Ce processeur est appelé par API Platform mais l'authentification est interceptée par l'authenticator
        
        return [
            'success' => true,
            'message' => 'OTP verified successfully'
        ];
    }
}