<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Student;
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

    public function __construct(MarkHelper $markHelper)
    {
        $this->markHelper = $markHelper;
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/average", name="average")
     */
    public function view(Request $request): Response
    {
        $mark = new Mark();
        $formStudent = $this->createFormBuilder($mark, [])
            ->setMethod('GET')
            ->add('student', EntityType::class, array(
                'class'        => Student::class,
                'choice_label' => function ($student) {
                    return $student ? $student->getFirstname().' '.$student->getName() : '';
                }
            ))
            ->add('valider', SubmitType::class)
            ->getForm();

        $averageClass = $this->markHelper->getAverageByClass();

        if ($request->isMethod('GET')) {
            $formStudent->handleRequest($request);

            if ($formStudent->isSubmitted()) {
                $student = $mark->getStudent();
                $arrayMarks = $this->markHelper->getMarksByStudent($student);
                $averageStudent = $this->markHelper->getAverageByStudent($student);

                return $this->render('average/average.html.twig',
                    ['form_student' => $formStudent->createView(),
                     'average_class' => $averageClass,
                     'marks_student' => $arrayMarks,
                     'average_student' => $averageStudent]
                );
            }
        }

        return $this->render('average/average.html.twig',
            ['form_student' => $formStudent->createView(), 'average_class' => $averageClass]
        );
    }
}
