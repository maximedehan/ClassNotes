<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Student;
use App\Repository\StudentRepository;
use App\Service\MarkHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AverageController extends AbstractController
{
    private $markHelper;
    private $studentRepository;

    public function __construct(MarkHelper $markHelper, StudentRepository $studentRepository)
    {
        $this->markHelper = $markHelper;
        $this->studentRepository = $studentRepository;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/average", name="average", methods={"GET"})
     */
    public function view(Request $request): Response
    {
        // On crée le FormBuilder grâce au service form factory
        $formStudent = $this->createFormBuilder(new Mark(), [])
            ->setMethod('GET')
            ->add('student',EntityType::class, array(
                'class' => Student::class,
                'choice_label' => function ($student) {
                    return $student ? $student->getFirstname().' '.$student->getName() : '';
                }
            ))
            ->add('valider',SubmitType::class)
            ->getForm();

        // On récupère la moyenne générale de la classe
        $averageClass = $this->markHelper->getAverageByClass();

        // Si l'utilisateur valide le choix de l'élève ...
        if ($request->isMethod('GET') && null !== $request->query->get('form')) {
            // On récupère ses moyennes
            $idStudent = intval($request->query->get('form')['student']);
            $student = $this->studentRepository->find($idStudent);
            $arrayMarks = $this->markHelper->getMarksByStudent($student);
            $averageStudent = $this->markHelper->getAverageByStudent($student);

            // On génère le tableau des notes avec la moyenne générale de l'élève
            return $this->render('average/average.html.twig',
                ['form_student' => $formStudent->createView(),
                 'average_class' => $averageClass,
                 'marks_student' => $arrayMarks,
                 'average_student' => $averageStudent]
            );
        }

        // Au premier chargement de la page des moyennes
        return $this->render('average/average.html.twig',
            ['form_student' => $formStudent->createView(), 'average_class' => $averageClass]
        );
    }
}
