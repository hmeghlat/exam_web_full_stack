<?php

namespace App\Controller;

use App\Entity\Hamster;
use App\Repository\HamsterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\UserService;



final class HamsterController extends AbstractController
{
    #[Route('/api/hamsters', name: 'all_hamsters', methods: ['GET'])]
    public function getAllHamsters(HamsterRepository $repo): JsonResponse
    {
        $listHamsters = $repo->findAll();
        return $this->json(
            [
                'listHamsters' => $listHamsters,

            ],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => 'hamster']
        );
    }

    #[Route('/api/hamsters/{id}', name: 'get_hamster', methods: ['GET'])]
    public function getHamsterbyId(Hamster $hamster): JsonResponse
    {
        return $this->json(
            [
                'hamster' => $hamster,
            ],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => 'hamster']
        );
    }
    #[Route('/api/hamsters/{id}/rename', name: 'rename_hamster', methods: ['PUT'])]
    public function renameHamster(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        HamsterRepository $hamsterRepository
    ): JsonResponse {
        $hamster = $hamsterRepository->find($id);

        if (!$hamster) {
            return $this->json(['error' => 'Hamster non trouvé'],JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        if (isset($data['name'])) {
            $hamster->setName($data['name']);
            $entityManager->flush();
            return $this->json(['message' => 'Hamster renommé avec succès'], JsonResponse::HTTP_OK);
        } else {
            return $this->json(['error' => 'Nom manquant'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/hamsters/sleep/{nbDays}', name: 'sleep_hamster', methods: ['POST'])]
    public function sleepHamster(
        int $nbDays,
        HamsterRepository $hamsterRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Utilisateur non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $hamster = $hamsterRepository->findOneBy(['owner' => $user]);

        if (!$hamster) {
            return $this->json(['error' => 'Hamster non trouvé pour cet utilisateur'], JsonResponse::HTTP_NOT_FOUND);
        }

        $newHunger =  $hamster->getHunger() - $nbDays;
        $newAge = $hamster->getAge() + $nbDays;
        $hamster->setHunger($newHunger);
        $hamster->setAge($newAge);
        $entityManager->flush();

        return $this->json(
            [
                'message' => "Le hamster a dormi pendant $nbDays jours.",
                'newHunger' => $newHunger
            ],
            JsonResponse::HTTP_OK
        );
    }

    #[Route('/api/hamsters/{id}/feed', name: 'feed_hamster', methods: ['POST'])]
    public function feedHamster(
        int $id,
        HamsterRepository $hamsterRepository,
        EntityManagerInterface $entityManager, UserService $userService
    ): JsonResponse {
        $hamster = $hamsterRepository->find($id);

        if (!$hamster) {
            return $this->json(['error' => 'Hamster non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $newHunger = $hamster->getHunger() +(100 - $hamster->getHunger());
        $hamster->setHunger($newHunger);
        $newgold = $userService->getGold() -(100 - $hamster->getHunger());
        $userService->updateGold($newgold);
        $entityManager->flush();

        return $this->json(
            [
                'message' => 'Hamster nourri avec succès.',
                'newHunger' => $newHunger,
                'newGold' => $newgold
            ],
            JsonResponse::HTTP_OK
        );
    }




}
