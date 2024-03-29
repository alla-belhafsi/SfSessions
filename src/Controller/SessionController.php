<?php

namespace App\Controller;
use App\Entity\Session;
use App\Entity\Programme;
use App\Entity\Stagiaire;
use App\Form\SessionType;
use Symfony\Component\Mime\Address;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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

    # Définir une nouvelle route pour ajouter un stagiaire à la session
    #[Route('/session/{id}/add-stagiaire/{stagiaireId}', name: 'add_stagiaire')]
    public function addStagiaire(Session $session, $stagiaireId, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {

        // Récupère le stagiaire à partir de l'ID
        $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);

        if ($stagiaire) {
            // Inscrire le stagiaire à la session
            $session->addStagiaire($stagiaire);
            // Sauvegarder les changements en BDD
            $entityManager->flush();
            // Envoyer l'e-mail de confirmation
            $this->sendConfirmationEmail($stagiaire, $session, $mailer);

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

    // #[Route('/session/{id}/add-stagiaire/{stagiaireId}', name: 'add_stagiaire')]
    // public function addStagiaire(Session $session, $stagiaireId, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    // {

    //     // Récupère le stagiaire à partir de l'ID
    //     $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);

    //     if ($stagiaire && $stagiaire->getEtat() === 'CONFIRMEE') {
    //         // Inscrire le stagiaire à la session
    //         $session->addStagiaire($stagiaire);
    //         // Sauvegarder les changements en BDD
    //         $entityManager->flush();
    //         // Envoyer l'e-mail de confirmation
    //         $this->sendConfirmationEmail($stagiaire, $session, $mailer);

    //         // Redirigez vers la page de détail de la session après l'inscription
    //         return $this->redirectToRoute('show_session', [
    //             'id' => $session->getId()
    //         ]);
    //     }

    //     // Rediriger vers la page de détail de la session après la suppression
    //     return $this->redirectToRoute('show_session', [
    //         'id' => $session->getId()
    //     ]);
    // }

    # Définir une nouvelle route pour supprimer un stagiaire de la session
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

    // Fonction pour envoyer un e-mail de confirmation d'inscription à une session
    private function sendConfirmationEmail(Stagiaire $stagiaire, Session $session, MailerInterface $mailer)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('jade@elan-formation.fr', 'Elan formation'))
            ->to($stagiaire->getMail())
            ->subject('Demande d\'inscription')
            ->htmlTemplate('stagiaire/confirmation_email.html.twig')
            ->context([
                'stagiaire' => $stagiaire,
                'session' => $session,
            ]);

        $mailer->send($email);
    }

    # Définir une nouvelle route pour confirmer les inscriptions
    // #[Route('/session/{id}/confirmInscription/{stagiaireId}', name: 'session_confirm_inscription')]
    // public function confirmInscription($id, $stagiaireId): Response
    // {
    //     // Récupère le stagiaire à partir de l'ID
    //     $stagiaire = $entityManager->getRepository(Stagiaire::class)->find($stagiaireId);

    //     $entityManager = $this->getDoctrine()->getManager();

    //     // Mettre à jour l'état de StagiaireSession dans la base de données
    //     $stagiaireSession->setEtat('CONFIRMEE');
    //     $entityManager->flush();

    //     return $this->redirectToRoute('liste_sessions');
    // }

    // #[Route('/session/{id}/rejectInscription/{stagiaireId}', name: 'session_reject_inscription')]
    // public function rejectInscription($id, $stagiaireId): Response
    // {
    //     $entityManager = $this->getDoctrine()->getManager();

    //     // Mettez à jour l'état de la StagiaireSession dans la base de données
    //     $stagiaireSession->setEtat('REJETEE');
    //     $entityManager->flush();

    //     return $this->redirectToRoute('liste_sessions');
    // }

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
