<?php
namespace App\EventListener;

use App\Entity\AuthUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RefreshToken;

class AuthenticationSuccessListener
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof AuthUser) {
            return;
        }

        $username = $user->getUsername();

        $refreshTokenRepository = $this->em->getRepository(RefreshToken::class);
        $refreshTokens = $refreshTokenRepository->findBy(['username' => $username], ['valid' => 'DESC']);
        
        array_shift($refreshTokens);

        foreach ($refreshTokens as $refreshToken) {
            $this->em->remove($refreshToken);
        }

        $this->em->flush();

        $event->setData($data);
    }
}
