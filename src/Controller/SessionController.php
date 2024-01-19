<?php

namespace App\Controller;
use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'app_session')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findBy([], ["intitule" => "ASC"]);

        return $this->render('session/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    # Définir une nouvelle route pour créer une nouvelle Session
    #[Route('/session/new', name: 'new_session')]
    # Définir une nouvelle route pour éditer une session
    #[Route('/session/{id}/edit', name: 'edit_session')]
    public function new_edit(Session $session = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Si la session n'existe pas, créer une nouvelle instance de l'entité Session 
        if(!$session) 
        {
            $session = new Session();
            // Ajouter un programme vide à la session si elle n'a pas de programmes
            if ($session->getProgrammes()->isEmpty()) {
                $programme = new Programme();
                $session->addProgramme($programme);
            }
        }
        
        
        // Créer un formulaire basé sur le type de formulaire SessionType et l'entité Session
        $form = $this->createForm(SessionType::class, $session);

        // Traiter la requête HTTP pour remplir le formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données soumises dans le formulaire ($form->getData() contient les valeurs soumises dans le formulaire)
            $session = $form->getData();
            
            
            // Préparer l'entité à être persistée (ajoutée) en base de données (Prepare PDO)
            $entityManager->persist($session);
            
            // Parcourir la collection de Programmes et les persister
            foreach ($session->getProgrammes() as $programme) 
            {
                // Persister le Programme avant de l'ajouter à la Session
                $entityManager->persist($programme);
            }
            // Exécuter la persistance des données en base de données (Execute PDO)
            $entityManager->flush();

            // Rediriger vers les détails de la session après l'ajout réussi
            return $this->redirectToRoute('show_session', [
                'id' => $session->getId()
            ]);
        }
        
        // Si le formulaire n'a pas été soumis ou n'est pas valide, afficher le formulaire
        return $this->render('session/new.html.twig', [
            'formAddSession' => $form,
            'edit' => $session->getId(),
            'session' => $session
        ]);
    }

    # Définir une nouvelle route pour supprimer une session
    #[Route('/session/{id}/delete', name: 'delete_session')]
    public function delete (Session $session, EntityManagerInterface $entityManager): Response
    {
        // Préparer l'entité à être supprimer de la base de données (Prepare PDO)
        $entityManager->remove($session);

        // Exécuter la suppression des données en base de données (Execute PDO)
        $entityManager->flush();

        // Rediriger vers la liste des sessions après la suppression réussi
        return $this->redirectToRoute('app_session');
    }

    # Définir une nouvelle route pour supprimer une session
    #[Route('/session/{id}/add-stagiaire/{stagiaireId}', name: 'add_stagiaire')]
    public function addStagiaire(Session $session, $stagiaireId, EntityManagerInterface $entityManager): Response
    {

        // Récupère le stagiaire à partir de l'ID
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);

        if ($stagiaire) {
            // Inscrire le stagiaire à la session
            $session->addStagiaire($stagiaire);
            $entityManager->flush();

            // Redirigez vers la page de détail de la session après l'inscription
            return $this->redirectToRoute('show_session', [
                'id' => $session->getId()
            ]);
        }

        // Rediriger vers la page de détail de la session après la suppression
        return $this->redirectToRoute('show_session', [
            'id' => $session->getId()
        ]);
    }

    # Définir une nouvelle route pour supprimer une session
    #[Route('/session/{id}/remove-stagiaire/{stagiaireId}', name: 'remove_stagiaire')]
    public function removeStagiaire(Session $session, $stagiaireId, EntityManagerInterface $entityManager): Response
    {
        try {
            $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);
    
            if (!$stagiaire) {
                throw $this->createNotFoundException('Stagiaire non trouvé.');
            }
    
            // Supprimer le stagiaire de la session
            $session->removeStagiaire($stagiaire);
            $entityManager->flush();
    
            $this->addFlash('success', 'Stagiaire supprimé de la session avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        // Rediriger vers la page de détail de la session après la suppression
        return $this->redirectToRoute('show_session', [
            'id' => $session->getId()
        ]);
    }

    # Définir une nouvelle route pour afficher les sessions
    #[Route('/session/{id}', name: 'show_session')]
    public function show (Session $session = null, SessionRepository $sr): Response
    {
        $nonInscrits = $sr->findNonInscrits($session->getId());
        // $nonProgrammes = $sr->findNonProgrammes($session->getId());

        $programmes = $session->getProgrammes();

        // Récupérer les modules pour chaque programme
        $modules = [];
        foreach ($session->getProgrammes() as $programme) {
            $module = $programme->getModule();
            if ($module) {
                $modules[] = $module;
            }
        }

        $nonInscrits = $sr->findNonInscrits($session->getId());

        return $this->render('session/show.html.twig', [
            'session' => $session,
            'modules' => $modules,
            'programmes' => $session->getProgrammes(),
            'nonInscrits' => $nonInscrits,
            // 'nonProgrammes' => $nonProgrammes,
            'id' => $session->getId()
        ]);
    }
    
}
