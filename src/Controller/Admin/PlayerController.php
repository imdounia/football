<?php

namespace App\Controller\Admin;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class PlayerController extends AbstractController
{
    public function __construct(private PlayerRepository $playerRepository, private RequestStack $requestStack, 
    private EntityManagerInterface $entityManager)
    {

    }

    #[Route('/player', name: 'admin.player.index')]
    public function index():Response
    {
        return $this->render('admin/player/index.html.twig', [
            'entities' => $this->playerRepository->findAll(),
        ]);
    }

    #[Route('/player/form/add', name: 'admin.player.form.add')]
    #[Route('/player/form/edit/{id}', name: 'admin.player.form.edit')]
    public function form(int $id = null):Response
    {
        $type = PlayerType::class;

        //if id is null, the player is being created, or else the player is being modified
        $model = $id ? $this->playerRepository->find($id) : new Player();

        $model->prevImage = $id ? $model->getPortrait() : null;
        // dd($model);

        $form = $this->createForm($type, $model);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if($form->isSubmitted() && $form->isValid()){
            // dd($model);

            //if an image is selected
            if($form['portrait']->getData() instanceof UploadedFile){
                $file = $form['portrait']->getData();

                //generate a random name
                $randomName = ByteString::fromRandom(32)->lower();
                $fileExtension = $file->guessClientExtension();
                $fullFileName = "$randomName.$fileExtension";

                //place the file 
                $file->move('img/portrait/', $fullFileName);

                $model->setPortrait($fullFileName);

                if($id) {
                    unlink("img/portrait/{$model->prevImage}");
                }
                // dd($randomName, $fileExtension, $fullFileName);
            }
            else{
                $model->setPortrait($model->prevImage);
            }
            
            //access database
            $this->entityManager->persist($model);
            $this->entityManager->flush();

            //create a flash message
            $message = $id ? "Player has been updated" : "Player has been added";
            $this->addFlash('notice', $message);

            //redirection
            return $this->redirectToRoute('admin.player.index');
        }

        return $this->render('admin/player/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/player/remove/{id}', name: 'admin.player.remove')]
    public function remove(int $id): Response
    {
        //select the entity that we want to delete
        $entity = $this->playerRepository->find($id);

        //delete entity
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        //message flash at redirection
        $this->addFlash('notice', 'Player has been removed');
        return $this->redirectToRoute('admin.player.index');
    }
}

?>