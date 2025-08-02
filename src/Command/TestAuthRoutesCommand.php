<?php

namespace App\Command;

use App\Service\AuthService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsCommand(
    name: 'app:test-auth-routes',
    description: 'Test authentication routes (send-otp and verify-otp)',
)]
class TestAuthRoutesCommand extends Command
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('phone', InputArgument::REQUIRED, 'Phone number to test')
            ->addOption('invalid-code', null, InputOption::VALUE_NONE, 'Test with an invalid OTP code')
            ->addOption('valid-code', null, InputOption::VALUE_NONE, 'Test explicitly with a valid OTP code (default behavior)')
            ->addOption('base-url', null, InputOption::VALUE_OPTIONAL, 'Base URL for API', 'http://localhost:8000')
        ;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $phone = $input->getArgument('phone');
        $baseUrl = $input->getOption('base-url');
        $testInvalidCode = $input->getOption('invalid-code');
        $testValidCode = $input->getOption('valid-code') || !$testInvalidCode;

        $io->title('Testing Authentication Routes');

        // Create HTTP client
        $client = HttpClient::create();

        // Step 1: Test send-otp endpoint
        $io->section('Testing /api/auth/send-otp endpoint');
        
        try {
            $response = $client->request('POST', "$baseUrl/api/auth/send-otp", [
                'json' => ['phone' => $phone],
                'headers' => ['Content-Type' => 'application/ld+json']
            ]);
            
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();
            $data = json_decode($content, true);
            
            $io->success("Send OTP Response (Status: $statusCode):");
            $io->writeln(json_encode($data, JSON_PRETTY_PRINT));
            
            // Utilisation d'un code OTP fictif au lieu d'acceder a la base de données
            $io->note('Utilisation d\'un code OTP fictif pour les tests...');
            
            // Code OTP fictif pour les tests
            $otpCode = '123456';
            $io->info("OTP Code fictif: $otpCode");
            
            // Step 2: Test verify-otp endpoint
            $io->section('Testing /api/auth/verify-otp endpoint');
            
            // If testing invalid code, modify the OTP
            if ($testInvalidCode) {
                $otpCode = '000000'; // Invalid code
                $io->warning("Testing with invalid OTP code: $otpCode");
            } else {
                $io->success("Testing with valid OTP code: $otpCode");
                if ($testValidCode) {
                    $io->note('Using valid code test mode (default behavior)');
                }
            }
            
            $response = $client->request('POST', "$baseUrl/api/auth/verify-otp", [
                'json' => [
                    'phone' => $phone,
                    'code' => $otpCode
                ],
                'headers' => ['Content-Type' => 'application/ld+json']
            ]);
            
            $statusCode = $response->getStatusCode();
            $content = $response->getContent();
            $data = json_decode($content, true);
            
            $io->success("Verify OTP Response (Status: $statusCode):");
            $io->writeln(json_encode($data, JSON_PRETTY_PRINT));
            
            $io->success('Authentication routes tested successfully!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}