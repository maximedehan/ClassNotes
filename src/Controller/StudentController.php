<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class StudentController extends AbstractController
{
    private $studentRepository;

    public function __construct(StudentRepository $studentRepository)
    {
        $this->studentRepository = $studentRepository;
    }

    /**
     * @Route("/student", name="student")
     */
    public function view(): Response
    {
        $students = $this->studentRepository->findAll();

        return $this->render('student/student.html.twig', ['students' => $students]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @Route("/student/add", name="student_add")
     */
    public function add(Request $request)
    {
        // On crée un objet Student
        $student = new Student();

        // On crée le FormBuilder grâce au service form factory
        // Puis, on ajoute les champs de l'entité que l'on veut à notre formulaire avant de le génèrer
        $form = $this->createFormBuilder($student, [])
            ->add('firstname',       TextType::class, array('required' => true))
            ->add('name',   TextType::class, array('required' => true))
            ->add('birthday',   TextType::class, array('required' => false))
            ->add('save',      SubmitType::class)
            ->getForm()
        ;

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $student contient les valeurs rentrées dans le formulaire.
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $student dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($student);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Elève bien enregistré.');

                // On redirige vers la page d'accueil de la gestion d'élèves
                return $this->redirectToRoute('student');
            }
        }

        // Dans le cas d'un formulaire invalide
        return $this->render('student/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Student $student
     * @return RedirectResponse|Response
     * @Route("/student/update/{id}", name="student_update")
     */
    public function update(Request $request, Student $student)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($student, [])
            ->add('firstname',       TextType::class, array('required' => true))
            ->add('name',   TextType::class, array('required' => true))
            ->add('birthday',   TextType::class, array('required' => false))
            ->add('save',      SubmitType::class)
            ->getForm()
        ;

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Elève bien modifié.');

            return $this->redirectToRoute('student');
        }

        return $this->render('student/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Student $student
     * @return RedirectResponse
     * @Route("/student/delete/{id}", name="student_delete")
     */
    public function delete(Student $student)
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($student->getMarks() as $mark) {
            $this->redirectToRoute('mark_delete', ['id' => $mark->getId()]);
        }

        $em->remove($student);
        $em->flush();

        return $this->redirectToRoute('student');
    }
}
