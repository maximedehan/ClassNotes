<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Service\StudentHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StudentController extends AbstractController
{
    private $studentRepository;
    private $studentHelper;
    private $serializer;
    private $validator;

    public function __construct(
        StudentRepository $studentRepository,
        StudentHelper $studentHelper,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    )
    {
        $this->studentRepository = $studentRepository;
        $this->studentHelper = $studentHelper;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("/api/student", name="student", methods={"GET"})
     */
    public function view(): Response
    {
        $students = $this->studentRepository->findAll();
        $response = $this->json($students,200, [], ['groups' => 'student:read']);

        return $this->render('student/student.html.twig', [
            'students' => json_decode($response->getContent())
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @Route("/api/student/add", name="student_add", methods={"GET","POST"})
     */
    public function add(Request $request)
    {
        // On crée le FormBuilder grâce au service form factory
        $form = $this->createFormBuilder(new Student(), [])
            ->add('firstname',TextType::class, array('required' => true))
            ->add('name',TextType::class, array('required' => true))
            ->add('birthday',TextType::class, array('required' => false))
            ->add('save',SubmitType::class)
            ->getForm()
        ;

        // Si l'utilisateur valide le formulaire, appel de l'API en POST
        if ($request->isMethod('POST')) {
            try {
                // On récupère l'entité student générée par les données du formulaire
                $student = $this->studentHelper->getStudentFromFormRequest($request);

                //On fait un contrôle sur les attributs de l'entité générée
                $errors = $this->validator->validate($student);
                if (count($errors) > 0) {
                    return $this->json($errors,400);
                }

                // On enregistre l'entité $student dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($student);
                $em->flush();

                // On redirige vers la page d'accueil de la gestion d'élèves
                return $this->redirectToRoute('student');

            } catch (NotEncodableValueException $e) {
                return $this->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ],400);
            }
        }

        // Dans le cas d'un formulaire non validé par l'utilisateur
        return $this->render('student/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Student $student
     * @return RedirectResponse|Response
     * @Route("/api/student/update/{id}", name="student_update", methods={"GET","POST"})
     */
    public function update(Request $request, Student $student)
    {
        // On crée le FormBuilder grâce au service form factory
        $form = $this->createFormBuilder($student, [])
            ->add('firstname',TextType::class, array('required' => true))
            ->add('name',TextType::class, array('required' => true))
            ->add('birthday',TextType::class, array('required' => false))
            ->add('save',SubmitType::class)
            ->getForm()
        ;

        // Si l'utilisateur valide le formulaire, appel de l'API en POST
        if ($request->isMethod('POST')) {
            // On récupère l'entité student générée par les données du formulaire
            $newStudent = $this->studentHelper->getStudentFromFormRequest($request);
            $student->setFirstname($newStudent->getFirstname());
            $student->setName($newStudent->getName());
            $student->setBirthday($newStudent->getBirthday());

            //On fait un contrôle sur les attributs de l'entité générée
            $errors = $this->validator->validate($student);
            if (count($errors) > 0) {
                return $this->json($errors,400);
            }

            // On met à jour l'entité $student dans la base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($student);
            $em->flush();

            // On redirige vers la page d'accueil de la gestion d'élèves
            return $this->redirectToRoute('student');
        }

        // Dans le cas d'un formulaire non validé par l'utilisateur
        return $this->render('student/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Student $student
     * @return RedirectResponse
     * @Route("/api/student/delete/{id}", name="student_delete", methods={"GET","DELETE"})
     */
    public function delete(Student $student)
    {
        $em = $this->getDoctrine()->getManager();

        // On supprime toutes les notes de l'élève avant la suppression de l'élève
        foreach ($student->getMarks() as $mark) {
            $em->remove($mark);
            $em->flush();
        }

        // On supprime l'élève
        $em->remove($student);
        $em->flush();

        // On redirige vers la page d'accueil de la gestion d'élèves
        return $this->redirectToRoute('student');
    }
}
