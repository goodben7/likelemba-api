<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\ApiResource\Dto\OtpRequest;
use App\State\SendOtpProcessor;
use App\State\VerifyOtpProcessor;
use App\State\LogoutProcessor;

#[ApiResource(
    shortName: 'Auth',
    operations: [
        new Post(
            uriTemplate: '/auth/send-otp',
            input: OtpRequest::class,
            processor: SendOtpProcessor::class,
            name: 'Send OTP code to phone number',
            description: 'Sends a one-time password to the provided phone number'
        ),
        new Post(
            uriTemplate: '/auth/verify-otp',
            input: OtpRequest::class,
            processor: VerifyOtpProcessor::class,
            name: 'Verify OTP code',
            description: 'Verifies the OTP code sent to the phone number',
            stateless: false
        ),
        new Post(
            uriTemplate: '/auth/logout',
            processor: LogoutProcessor::class,
            name: 'Logout',
            description: 'Logs out the current user',
            security: 'is_granted("ROLE_USER")',
            stateless: false
        ),
    ]
)]
class Auth
{
    // Cette classe est une ressource API sans entité associée
    // Elle sert uniquement à définir les points d'entrée de l'API
}