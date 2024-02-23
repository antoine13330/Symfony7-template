<?php

namespace App\Controller;

use App\Repository\AuthUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;

#[AsController]
class RefreshTokenController extends AbstractController
{
    private JWTTokenManagerInterface  $jwtManager;
    private RefreshTokenManagerInterface $refreshTokenManager;
    private RefreshTokenGeneratorInterface $refreshTokenGenerator;
    private EventDispatcherInterface $eventDispatcher;
    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        RefreshTokenManagerInterface $refreshTokenManager,
        RefreshTokenGeneratorInterface $refreshTokenGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->eventDispatcher = $eventDispatcher;
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
            $refreshToken = json_decode($request->getContent(), true);
            $refreshToken = $refreshToken['refreshToken'] ?? null;
            if (!$refreshToken) {
                throw new HttpException(Response::HTTP_BAD_REQUEST,'Refresh token is missing');
            }

            $refreshToken = $this->refreshTokenManager->get($refreshToken);
            if (!$refreshToken) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED,'Refresh token not found in database');
            }

            $now = new \DateTime();
            if ($now > $refreshToken->getValid()) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED,'Refresh token is expired');
            }
            
            $user = $refreshToken->getUsername();
            $authUser = $authUserRepository->findOneBy(['username' => $user]);
            if (!$authUser) {
                throw new HttpException(Response::HTTP_UNAUTHORIZED,'User not found');
            }

            $token = $this->jwtManager->create($authUser);
            $refreshToken = $this->refreshTokenGenerator->createForUserWithTtl($authUser, 2592000);
            $this->refreshTokenManager->save($refreshToken);

            $event = new AuthenticationSuccessEvent(['token' => $token], $authUser, new JsonResponse());
            $this->eventDispatcher->dispatch($event, AuthenticationSuccessEvent::class);
            return new JsonResponse(['token' => $token, 'refreshToken' => $refreshToken->getRefreshToken()]);

        } catch (HttpException $e) {
            return new JsonResponse(['error' => $e->getMessage()], $e->getStatusCode());
        }
    }

     


}
