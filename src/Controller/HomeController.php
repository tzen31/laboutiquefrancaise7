<?php

namespace App\Controller;

use App\Entity\Header;
use App\Repository\HeaderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(HeaderRepository $headerRepository): Response
    {
        
        return $this->render('home/index.html.twig', [
            'headers' => $headerRepository->findAll()
        ]);
    }    

}
