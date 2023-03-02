<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Club;
use App\Repository\ClassroomRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\FormTypeInterface;




use Doctrine\Persistence\ManagerRegistry;
class ClubController extends AbstractController
{
    #[Route('/club', name: 'app_club')]
    public function index(): Response
    {
        return $this->render('club/index.html.twig');
    }
  
    #[Route('/AjoutClub', name: 'AjoutClub')]
    public function Ajout(Request $request, ManagerRegistry $entityManager): Response
    {
        $club = new Club();

        $form = $this->createFormBuilder($club)
            ->add('createdAt', TextType::class, [
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données du formulaire soumis
            $club = $form->getData();
            // si le champ createdAt est vide, le remplir avec la date actuelle
            if (empty($club->getCreatedAt())) {
                $club->setCreatedAt();
            }
            // persister l'entité en base de données
            $entityManager->getManager()->persist($club);
            $entityManager->getManager()->flush();
            $this->addFlash('success', 'Le club a été ajouté.');
            // rediriger vers la page d'affichage des clubs
            return $this->redirectToRoute('getClub');
        }

        return $this->render('club/Ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/deleteClub/{id}', name: 'deleteClub')]
    public function delete(Request $request, ManagerRegistry $entityManager, $id): Response
    {
        $Club = $entityManager->getRepository(Club::class)->find($id);
      $club=$entityManager->getManager()->remove($Club);
      $entityManager->getManager()->flush();
      return $this->redirectToRoute('getClub');
    }

    #[Route('/getClub', name: 'getClub')]
    public function list(StudentRepository $repo): Response
    {
        return $this->render('club/list.html.twig', [
            'stclub' => $repo->findAll(),
        ]);
    }
    
    
    #[Route('/editclub/{id}/{name}', name: 'editclub')]
    public function toedit(Request $request, ManagerRegistry $entityManager, $id, $name): Response
    {
        $classroom = $entityManager->getRepository(Club::class)->find($id);
        $form = $this->createFormBuilder($classroom)
            ->add('createdAt', TextType::class, ['data' => $name])
            ->getForm();
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $classroom = $form->getData();
            $entityManager->getManager()->flush();
            $this->addFlash('success', 'La date de club a été modifiée.');
            return $this->redirectToRoute('getClub');
        }
    
        return $this->render('club/modif.html.twig', [
            'form' => $form->createView(),
        ]);
    } 
    
      
    


}
