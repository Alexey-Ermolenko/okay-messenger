<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/registration', name: 'registration', methods: 'post')]
    public function index(
        Request $request,
        ManagerRegistry $doctrine,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse
    {
        //  https://www.binaryboxtuts.com/php-tutorials/symfony-6-json-web-tokenjwt-authentication/

        $em = $doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $email = $decoded->email;
        $plaintextPassword = $decoded->password;

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);
        $user->setEmail($email);
        $user->setUsername($email);
        $em->persist($user);
        $em->flush();

        return $this->json(['message' => 'Registered Successfully']);
    }
}
