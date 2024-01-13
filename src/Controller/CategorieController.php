<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(CategorieRepository $categorieRepository): Response
    {
        $categories = $categorieRepository->findBy([], ["nom" => "ASC"]);
        return $this->render('categorie/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    # Définir une nouvelle route pour créer une nouvelle Categorie
    #[Route('/categorie/new', name: 'new_categorie')]
    # Définir une nouvelle route pour éditer une categorie
    #[Route('/categorie/{id}/edit', name: 'edit_categorie')]
    public function new_edit(Categorie $categorie = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si la categorie n'existe pas, créer une nouvelle instance de l'entité Session 
        if(!$categorie) 
        {
            $categorie = new Categorie();
        }
        
        // Créer un formulaire basé sur le type de formulaire CategorieType et l'entité Categorie
        $form = $this->createForm(CategorieType::class, $categorie);

        // Traiter la requête HTTP pour remplir le formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données soumises dans le formulaire ($form->getData() contient les valeurs soumises dans le formulaire)
            $categorie = $form->getData();

            // Préparer l'entité à être persistée (ajoutée) en base de données (Prepare PDO)
            $entityManager->persist($categorie);

            // Exécuter la persistance des données en base de données (Execute PDO)
            $entityManager->flush();

            // Rediriger vers le panneau de configuration après l'ajout réussi
            return $this->redirectToRoute('new_categorie', []);
           }
        
        // Si le formulaire n'a pas été soumis ou n'est pas valide, afficher le formulaire
        return $this->render('categorie/new.html.twig', [
            'formAddCategorie' => $form,
            'edit' => $categorie->getId(),
            'categorie' => $categorie
        ]);
    }

    # Définir une nouvelle route pour supprimer une catégorie
    #[Route('/categorie/{id}/delete', name: 'delete_categorie')]
    public function delete (Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Préparer l'entité à être supprimer de la base de données (Prepare PDO)
        $entityManager->remove($categorie);

        // Exécuter la suppression des données en base de données (Execute PDO)
        $entityManager->flush();

        // Rediriger vers la liste des categories après la suppression réussi
        return $this->redirectToRoute('app_categorie');
    }

    #[Route('/categorie/{id}', name: 'show_module')]
    public function show (Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }
}
