<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\JobSeeker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class JobSeekerController extends AbstractController
{
    // #[Route('/api/job-seeker', name: 'api_add_job_seeker', methods: ['POST'])]
    public function addJobSeeker(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Vérifier si l'utilisateur est admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Seuls les administrateurs peuvent ajouter un JobSeeker.');
        }

        // Décoder les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Validation des données requises
        if (empty($data['lastName']) || empty($data['firstName']) || empty($data['age']) || empty($data['email'])) {
            return new JsonResponse(['error' => 'Tous les champs obligatoires doivent être remplis (lastName, firstName, age, email).'], 400);
        }

        // Créer un nouvel objet JobSeeker
        $jobSeeker = new JobSeeker();
        $jobSeeker->setLastName($data['lastName']);
        $jobSeeker->setFirstName($data['firstName']);
        $jobSeeker->setAge((int) $data['age']);
        $jobSeeker->setEmail($data['email']);
        $jobSeeker->setAddress($data['address'] ?? null);
        $jobSeeker->setPostalCode($data['postalCode'] ?? null);
        $jobSeeker->setCity($data['city'] ?? null);

        // Sauvegarder l'objet dans la base de données
        $entityManager->persist($jobSeeker);
        $entityManager->flush();

        // Retourner une réponse JSON
        return new JsonResponse([
            'message' => 'JobSeeker ajouté avec succès.',
            'id' => $jobSeeker->getId()
        ], 201);
    }
}
