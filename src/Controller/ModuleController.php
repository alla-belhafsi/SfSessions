<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    public function index(): Response
    {
        return $this->render('module/index.html.twig', [
            'controller_name' => 'ModuleController',
        ]);
    }

    # Définir une nouvelle route pour créer un nouveau module
    #[Route('/module/new', name: 'new_module')]
    # Définir une nouvelle route pour éditer un module
    #[Route('/module/{id}/edit', name: 'edit_module')]
    public function new_edit(Module $module = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si la module n'existe pas, créer une nouvelle instance de l'entité Session 
        if(!$module) 
        {
            $module = new Module();
        }
        
        // Créer un formulaire basé sur le type de formulaire ModuleType et l'entité module
        $form = $this->createForm(ModuleType::class, $module);

        // Traiter la requête HTTP pour remplir le formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données soumises dans le formulaire ($form->getData() contient les valeurs soumises dans le formulaire)
            $module = $form->getData();

            // Préparer l'entité à être persistée (ajoutée) en base de données (Prepare PDO)
            $entityManager->persist($module);

            // Exécuter la persistance des données en base de données (Execute PDO)
            $entityManager->flush();

            // Rediriger vers le panneau de configuration après l'ajout réussi
            return $this->redirectToRoute('new_module', []);
           }
        
        // Si le formulaire n'a pas été soumis ou n'est pas valide, afficher le formulaire
        return $this->render('module/new.html.twig', [
            'formAddModule' => $form,
            'edit' => $module->getId(),
            'module' => $module
        ]);
    }

    # Définir une nouvelle route pour supprimer une session
    #[Route('/module/{id}/delete', name: 'delete_module')]
    public function delete (Module $module, EntityManagerInterface $entityManager): Response
    {
        // Préparer l'entité à être supprimer de la base de données (Prepare PDO)
        $entityManager->remove($module);

        // Exécuter la suppression des données en base de données (Execute PDO)
        $entityManager->flush();

        // Rediriger vers la liste des modules après la suppression réussi
        return $this->redirectToRoute('app_module');
    }
}
