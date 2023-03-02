<?php

namespace App\Controller;
use App\Entity\Classroom;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ClassroomRepository;
use Doctrine\Persistence\ManagerRegistry;
class ClassroomController extends AbstractController
{
    #[Route('/classroom', name: 'app_classroom')]
    public function index(): Response
    {
        return $this->render('classroom/index.html.twig', [
            'controller_name' => 'ClassroomController',
        ]);
    }
    #[Route('/getclass', name: 'getclass')]
    public function list(ClassroomRepository $repo): Response
    {
        return $this->render('classroom/list.html.twig', [
            'class' => $repo->findAll(),
        ]);
    }
    #[Route('/Ajoutclass', name: 'Ajoutclass')]
    public function Ajout(Request $request, ManagerRegistry $entityManager): Response
    {
        $classe = new Classroom();
        $form = $this->createFormBuilder($classe)
            ->add('name', TextType::class)
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $classe = $form->getData();
            $entityManager->getManager()->persist($classe);
            $entityManager->getManager()->flush();
            $this->addFlash('success', 'La classe a été ajoutée.');
            return $this->redirectToRoute('getclass');
        }
    
        return $this->render('classroom/Ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

//pass to modifier
#[Route('/edittext/{id}/{name}', name: 'edittext')]
public function toedit(Request $request, ManagerRegistry $entityManager, $id, $name): Response
{
    $classroom = $entityManager->getRepository(Classroom::class)->find($id);
    $form = $this->createFormBuilder($classroom)
        ->add('name', TextType::class, ['data' => $name])
        ->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $classroom = $form->getData();
        $entityManager->getManager()->flush();
        $this->addFlash('success', 'La classe a été modifiée.');
        return $this->redirectToRoute('getclass');
    }

    return $this->render('classroom/modif.html.twig', [
        'form' => $form->createView(),
    ]);
}


#[Route('/deleteclass/{id}', name: 'deleteclass')]
public function delete(Request $request, ManagerRegistry $entityManager, $id): Response
{
    $classroom = $entityManager->getRepository(Classroom::class)->find($id);
  $classroom=$entityManager->getManager()->remove($classroom);
  $entityManager->getManager()->flush();
  return $this->redirectToRoute('getclass');

   

  
}
    
}
