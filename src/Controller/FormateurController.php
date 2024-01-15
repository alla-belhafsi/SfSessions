<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\FormateurType;
use App\Repository\FormateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    #[Route('/formateur', name: 'app_formateur')]
    public function index(FormateurRepository $formateurRepository): Response
    {
        $formateurs = $formateurRepository->findBy([], ["nom" => "ASC"]);

        return $this->render('formateur/index.html.twig', [
            'formateurs' => $formateurs,
        ]);
    }

    # Définir une nouvelle route pour créer un nouveau formateur
    #[Route('/formateur/new', name: 'new_formateur')]
    # Définir une nouvelle route pour éditer un formateur
    #[Route('/formateur/{id}/edit', name: 'edit_formateur')]
    public function new_edit(Formateur $formateur = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si la formateur n'existe pas, créer une nouvelle instance de l'entité Session 
        if(!$formateur) 
        {
            $formateur = new Formateur();
        }
        
        // Créer un formulaire basé sur le type de formulaire FormateurType et l'entité formateur
        $form = $this->createForm(FormateurType::class, $formateur);

        // Traiter la requête HTTP pour remplir le formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données soumises dans le formulaire ($form->getData() contient les valeurs soumises dans le formulaire)
            $formateur = $form->getData();

            // Préparer l'entité à être persistée (ajoutée) en base de données (Prepare PDO)
            $entityManager->persist($formateur);

            // Exécuter la persistance des données en base de données (Execute PDO)
            $entityManager->flush();

            // Rediriger vers le panneau de configuration après l'ajout réussi
            return $this->redirectToRoute('new_formateur', []);
           }
        
        // Si le formulaire n'a pas été soumis ou n'est pas valide, afficher le formulaire
        return $this->render('formateur/new.html.twig', [
            'formAddFormateur' => $form,
            'edit' => $formateur->getId(),
            'formateur' => $formateur
        ]);
    }

    # Définir une nouvelle route pour supprimer un formateur
    #[Route('/formateur/{id}/delete', name: 'delete_formateur')]
    public function delete (Formateur $formateur, EntityManagerInterface $entityManager): Response
    {
        // Préparer l'entité à être supprimer de la base de données (Prepare PDO)
        $entityManager->remove($formateur);

        // Exécuter la suppression des données en base de données (Execute PDO)
        $entityManager->flush();

        // Rediriger vers la liste des formateurs après la suppression réussi
        return $this->redirectToRoute('app_formateur');
    }
}
