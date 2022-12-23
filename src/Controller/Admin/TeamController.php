<?php

namespace App\Controller\Admin;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\ByteString;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
class TeamController extends AbstractController
{
    public function __construct(private TeamRepository $teamRepository, private RequestStack $requestStack, 
    private EntityManagerInterface $entityManager)
    {

    }

    #[Route('/nationalteam', name: 'admin.team.index')]
    public function index():Response
    {
        return $this->render('admin/team/index.html.twig', [
            'entities' => $this->teamRepository->findAll(),
        ]);
    }

    #[Route('/team/form/add', name: 'admin.team.form.add')]
    #[Route('/team/form/edit/{id}', name: 'admin.team.form.edit')]
    public function form(int $id = null):Response
    {
        $type = TeamType::class;

        //if id is null, the team is being created, or else the team is being modified
        $model = $id ? $this->teamRepository->find($id) : new Team();

        $model->prevImage = $id ? $model->getFlag() : null;
        // dd($model);

        $form = $this->createForm($type, $model);
        $form->handleRequest($this->requestStack->getCurrentRequest());

        if($form->isSubmitted() && $form->isValid()){
            // dd($model);

            //if an image is selected
            if($form['flag']->getData() instanceof UploadedFile){
                $file = $form['flag']->getData();

                //generate a random name
                $randomName = ByteString::fromRandom(32)->lower();
                $fileExtension = $file->guessClientExtension();
                $fullFileName = "$randomName.$fileExtension";

                //place the file 
                $file->move('img/flag/', $fullFileName);

                $model->setFlag($fullFileName);

                if($id) {
                    unlink("img/flag/{$model->prevImage}");
                }
                // dd($randomName, $fileExtension, $fullFileName);
            }
            else{
                $model->setFlag($model->prevImage);
            }
            
            //access database
            $this->entityManager->persist($model);
            $this->entityManager->flush();

            //create a flash message
            $message = $id ? "A team has been updated" : "A team has been added";
            $this->addFlash('notice', $message);

            //redirection
            return $this->redirectToRoute('admin.team.index');
        }

        return $this->render('admin/team/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/team/remove/{id}', name: 'admin.team.remove')]
    public function remove(int $id): Response
    {
        //select the entity that we want to delete
        $entity = $this->teamRepository->find($id);

        //delete entity
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        //message flash at redirection
        $this->addFlash('notice', 'Team has been removed');
        return $this->redirectToRoute('admin.team.index');
    }
}

?>