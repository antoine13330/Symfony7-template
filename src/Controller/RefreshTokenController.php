<?php

namespace App\Controller;

use App\Repository\AuthUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RefreshTokenController extends AbstractController
{
    private JWTTokenManagerInterface  $jwtManager;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private RefreshTokenGeneratorInterface $refreshTokenGenerator;
    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        RefreshTokenGeneratorInterface $refreshTokenGenerator,
    ) {
        $this->jwtManager = $jwtManager;
        $this->refreshTokenManager = $refreshTokenManager;
        $this->refreshTokenGenerator = $refreshTokenGenerator;
    }
    #[Route("/api/refresh/token", name: "refresh_token", methods: ['POST'])]
    public function refresh(
        Request $request,
        AuthUserRepository $authUserRepository
    ): JsonResponse
    {
        try {
            $refreshToken = json_decode($request->getContent(), true)['refreshToken'];
            if (!$refreshToken) {
                throw new \Exception('Refresh token is missing');
            }

            $refreshToken = $this->refreshTokenManager->get($refreshToken);
            if (!$refreshToken) {
                throw new \Exception('Refresh token not found in databse');
            }

            $now = new \DateTime();
            if ($now > $refreshToken->getValid()) {
                throw new \Exception('Refresh token is expired');
            }
            
            $user = $refreshToken->getUsername();
            $authUser = $authUserRepository->findOneBy(['username' => $user]);
            if (!$authUser) {
                throw new \Exception('User not found');
            }

            $token = $this->jwtManager->create($authUser);
            $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($authUser, 2592000);
            $this->refreshTokenManager->save($refreshToken);

            return new JsonResponse(['token' => $token, 'refreshToken' => $refreshToken->getRefreshToken()]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

     


}
