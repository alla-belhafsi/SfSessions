<?php

namespace App\Controller;

use App\Entity\Programme;
use App\Form\ProgrammeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProgrammeController extends AbstractController
{
    #[Route('/programme', name: 'app_programme')]
    public function index(): Response
    {
        return $this->render('programme/index.html.twig', [
            'controller_name' => 'ProgrammeController',
        ]);
    }

    # Définir une nouvelle route pour créer un nouveau programme
    #[Route('/programme/new', name: 'new_programme')]
    # Définir une nouvelle route pour éditer un programme
    #[Route('/programme/{id}/edit', name: 'edit_programme')]
    public function new_edit(Programme $programme = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si la programme n'existe pas, créer une nouvelle instance de l'entité Programme 
        if(!$programme) 
        {
            $programme = new Programme();
        }
        
        // Créer un formulaire basé sur le type de formulaire ProgrammeType et l'entité Programme
        $form = $this->createForm(ProgrammeType::class, $programme);

        // Traiter la requête HTTP pour remplir le formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données soumises dans le formulaire ($form->getData() contient les valeurs soumises dans le formulaire)
            $programme = $form->getData();

            // Préparer l'entité à être persistée (ajoutée) en base de données (Prepare PDO)
            $entityManager->persist($programme);

            // Exécuter la persistance des données en base de données (Execute PDO)
            $entityManager->flush();

            // Rediriger vers le panneau de configuration après l'ajout réussi
            return $this->redirectToRoute('new_programme', []);
           }
        
        // Si le formulaire n'a pas été soumis ou n'est pas valide, afficher le formulaire
        return $this->render('programme/new.html.twig', [
            'formAddProgramme' => $form,
            'edit' => $programme->getId(),
            'programme' => $programme
        ]);
    }

    # Définir une nouvelle route pour supprimer un programme
    #[Route('/programme/{id}/delete', name: 'delete_programme')]
    public function delete (Programme $programme, EntityManagerInterface $entityManager): Response
    {
        // Préparer l'entité à être supprimer de la base de données (Prepare PDO)
        $entityManager->remove($programme);

        // Exécuter la suppression des données en base de données (Execute PDO)
        $entityManager->flush();

        // Rediriger vers la liste des programmes après la suppression réussi
        return $this->redirectToRoute('app_programme');
    }
}
