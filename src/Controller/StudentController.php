<?php
namespace App\Controller;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Classroom;
use App\Entity\Student;
use App\Repository\ClassroomRepository;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Form\FormTypeInterface;



use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }

    
    #[Route('/Ajoutstudent', name: 'Ajoutstudent')]
    public function Ajout(Request $request, ManagerRegistry $entityManager): Response
    {
        $student = new Student();
        $form = $this->createFormBuilder($student)
            ->add('email', TextType::class, [
        'required' => true,
        'constraints' => [new NotBlank()]
    ])
      
    
    /*->add('classroom', EntityType::class,['class' => Classroom::class,'choice_label' => 'name',])
    */        ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $student = $form->getData();
            $entityManager->getManager()->persist($student);
            $entityManager->getManager()->flush();
            $this->addFlash('success', 'La classe a été ajoutée.');
            return $this->redirectToRoute('getstudent');
        }
    
        return $this->render('student/Ajout.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/getstudent', name: 'getstudent')]
    public function list(StudentRepository $repo): Response
    {
        return $this->render('student/list.html.twig', [
            'stud' => $repo->findAll(),
        ]);
    }
    #[Route('/deleteStudent/{id}', name: 'deleteStudent')]
    public function delete(Request $request, ManagerRegistry $entityManager, $id): Response
    {
        $classroom = $entityManager->getRepository(Student::class)->find($id);
      $classroom=$entityManager->getManager()->remove($classroom);
      $entityManager->getManager()->flush();
      return $this->redirectToRoute('getstudent');
    
       
    
      
    }

    #[Route('/editstudent/{id}/{name}', name: 'editstudent')]
    public function toedit(Request $request, ManagerRegistry $entityManager, $id, $name): Response
    {
        $classroom = $entityManager->getRepository(Student::class)->find($id);
        $form = $this->createFormBuilder($classroom)
            ->add('email', TextType::class, ['data' => $name])
            ->getForm();
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $classroom = $form->getData();
            $entityManager->getManager()->flush();
            $this->addFlash('success', 'Le student a été modifiée.');
            return $this->redirectToRoute('getstudent');
        }
    
        return $this->render('student/modif.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
