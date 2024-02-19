<?php

namespace App\Controller;

use App\Entity\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
#[AsController]
class RegisterController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;
    private $JWTManager;
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        JWTTokenManagerInterface $JWTManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->JWTManager = $JWTManager;
        $this->validator = $validator;
    }
    #[Route("/api/register", name: "register", methods: ["POST"])]
    public function register(Request $request , SerializerInterface $serializer)
    {
        $data = json_decode($request->getContent(), true);

        if ( empty($data['username']) || empty($data['password']) ) {
            return new JsonResponse(['error' => 'Expecting mandatory parameters!']);
        }

        $user = new AuthUser();
        $user->setUsername($data['username']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);
        $validationErrors = $this->validator->validate($user);
        if (count($validationErrors) > 0) {
            throw new \Exception((string) $validationErrors[0]);
        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $token = $this->JWTManager->create($user);
        $user = $serializer->serialize([
            'user' => $user,
            'token' => $token
        ], 'json', ['groups' => 'user:deep']);
        return new JsonResponse( $user , 200 , [] , true);
    }
}