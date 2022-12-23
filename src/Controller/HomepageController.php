<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomepageController extends AbstractController
{
    public function __construct(private RequestStack $requestStack)
    {

    }


    #[Route('/', name: 'homepage.index')]
    public function index():Response 
    {
            return $this->render('homepage/index.html.twig', [
            ]);
    }
}

?>