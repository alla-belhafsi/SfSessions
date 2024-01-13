<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormateurTypeController extends AbstractController
{
    #[Route('/formateur/type', name: 'app_formateur_type')]
    public function index(): Response
    {
        return $this->render('formateur_type/index.html.twig', [
            'controller_name' => 'FormateurTypeController',
        ]);
    }
}
