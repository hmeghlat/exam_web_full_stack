<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Service\UserService;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class UserController extends AbstractController

{
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $jwtManager,
        UserService $userService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(
                [
                    'error' => 'Email and password are required fields.'
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_USER']);
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json(['error' => $errorsString], JsonResponse::HTTP_BAD_REQUEST);
        }
        $entityManager->persist($user);
        $entityManager->flush();
        // Générer le token JWT
        $token = $jwtManager->create($user);

        return $this->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'gold' => $userService->getGold()
            ],
            'token' => $token
        ], JsonResponse::HTTP_CREATED);
    }



    #[Route('/api/user', name: 'app_user', methods: ['GET'])]
    public function getUserInfo(
        #[CurrentUser] ?User $user,
        UserService $userService
    ): JsonResponse {
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'gold' => $userService->getGold()
        ], JsonResponse::HTTP_OK);
    }


    #[Route('/api/delete/{id}', name: 'app_user_delete', methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(['message' => 'Utilisateur supprimé avec succès'], JsonResponse::HTTP_OK);
    }
}
