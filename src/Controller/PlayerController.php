<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PlayerController extends AbstractController
{
    public function __construct(private PlayerRepository $playerRepository)
    {

    }

    #[Route('/players', name: 'player.index')]
    public function index():Response
    {
        $entities = $this->playerRepository->findAll();
        // dd($entities);
        return $this->render('player/index.html.twig', [
            'entities' => $entities,
        ]);
    }

    #[Route('/players/{firstname}-{lastname}/{id}', name: 'player.details')]
    public function details(string $firstname, string $lastname, int $id):Response
    {
        $entity = $this->playerRepository->findOneBy([
            'firstname' => $firstname,
            'lastname' => $lastname,
            'id' => $id,
        ]);
        // dump($entity);
        return $this->render('player/details.html.twig', [
            'entity' => $entity,
        ]);
    }
}

?>