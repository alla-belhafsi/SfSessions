<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/home/{id}', name: 'adminShow_home')]
    public function adminShow(UserRepository $userRepository): Response
    {
        // Tri des users par pseudo en ordre croissant (ASC)
        $users = $userRepository->findBy([], ["email" => "ASC"]);

        return $this->render('home/adminShow.html.twig', [
            'users' => $users
        ]);
    }
}
