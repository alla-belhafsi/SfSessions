<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\SessionRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findBy([], ["intitule" => "ASC"]);
        
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
        ]);
    }

    # Définir une nouvelle route pour créer une nouvelle formation
    #[Route('/formation/new', name: 'new_formation')]
    # Définir une nouvelle route pour éditer une formation
    #[Route('/formation/{id}/edit', name: 'edit_formation')]
    public function new_edit(Formation $formation = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si la formation n'existe pas, créer une nouvelle instance de l'entité Formation
        if(!$formation) 
        {
            $formation = new Formation();
        }
        
        // Créer un formulaire basé sur le type de formulaire FormationType et l'entité Formation
        $form = $this->createForm(FormationType::class, $formation);

        // Traiter la requête HTTP pour remplir le formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) 
        {
            // Récupérer les données soumises dans le formulaire ($form->getData() contient les valeurs soumises dans le formulaire)
            $formation = $form->getData();

            // Préparer l'entité à être persistée (ajoutée) en base de données (Prepare PDO)
            $entityManager->persist($formation);

            // Exécuter la persistance des données en base de données (Execute PDO)
            $entityManager->flush();

            // Rediriger vers le panneau de configuration après l'ajout réussi
            return $this->redirectToRoute('new_formation', []);
        }
        
        // Si le formulaire n'a pas été soumis ou n'est pas valide, afficher le formulaire
        return $this->render('formation/new.html.twig', [
            'formAddFormation' => $form,
            'edit' => $formation->getId(),
            'formation' => $formation
        ]);
    }

    # Définir une nouvelle route pour supprimer une formation
    #[Route('/formation/{id}/delete', name: 'delete_formation')]
    public function delete (Formation $formation, EntityManagerInterface $entityManager): Response
    {
        // Préparer l'entité à être supprimer de la base de données (Prepare PDO)
        $entityManager->remove($formation);

        // Exécuter la suppression des données en base de données (Execute PDO)
        $entityManager->flush();

        // Rediriger vers la liste des formations après la suppression réussi
        return $this->redirectToRoute('app_formation');
    }

    #[Route('/formation/{id}', name: 'show_formation')]
    public function show(SessionRepository $sessionRepository, Formation $formation): Response
    {
        // Récupération des sessions liées à la formation et tri par intitulé en ordre croissant (ASC)
        $sessions = $sessionRepository->findBy(['formation' => $formation], ['intitule' => 'ASC']);

        return $this->render('formation/show.html.twig', [
            'sessions' => $sessions,
            'formation' => $formation
        ]);
    }
}

