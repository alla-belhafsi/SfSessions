<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\SessionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SessionRepository $sessionRepository): Response
    {
        $sessions = $sessionRepository->findBy([], ["intitule" => "ASC"]);

        return $this->render('home/index.html.twig', [
            'sessions' => $sessions,
        ]);
    }

    #[Route('/home/{id}', name: 'show_home')]
    public function show(UserRepository $userRepository): Response
    {
        // Tri des users par pseudo en ordre croissant (ASC)
        $users = $userRepository->findBy([], ["email" => "ASC"]);

        return $this->render('home/adminShow.html.twig', [
            'users' => $users
        ]);
    }
}
