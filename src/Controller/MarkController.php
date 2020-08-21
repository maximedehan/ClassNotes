<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Student;
use App\Repository\MarkRepository;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class MarkController extends AbstractController
{
    /**
     * @Route("/mark", name="mark")
     */
    public function view(MarkRepository $markRepository): Response
    {
        $marks = $markRepository->findAll();

        return $this->render('mark/mark.html.twig', ['marks' => $marks]);
    }

    /**
     * @Route("/mark/add", name="mark_add")
     */
    public function addAction(Request $request)
    {
        $mark = new Mark();

        $form = $this->createFormBuilder($mark, [])
            ->add('student', EntityType::class, array(
                    'class'        => Student::class,
                    'choice_label' => function ($student) {
                        return $student ? $student->getFirstname().' '.$student->getName() : '';
                    }
            ))
            ->add('lesson',   TextType::class, array('required' => true))
            ->add('value',      NumberType::class, array('required' => true))
            ->add('save',      SubmitType::class)
            ->getForm()
        ;

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($mark);
                $em->flush();

                $request->getSession()->getFlashBag()->add('notice', 'Note bien enregistrée.');

                return $this->redirectToRoute('mark');
            }
        }

        return $this->render('mark/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/mark/update/{id}", name="mark_update")
     */
    public function updateAction(Mark $mark, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($mark, [])
            ->add('student', EntityType::class, array(
                'class'        => Student::class,
                'choice_label' => function ($student) {
                    return $student ? $student->getFirstname().' '.$student->getName() : '';
                }
            ))
            ->add('lesson',   TextType::class, array('required' => true))
            ->add('value',      NumberType::class, array('required' => true))
            ->add('save',      SubmitType::class)
            ->getForm()
        ;

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'Note bien modifiée.');

            return $this->redirectToRoute('mark');
        }

        return $this->render('mark/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/mark/delete/{id}", name="mark_delete")
     */
    public function deleteAction(Mark $mark)
    {
        $em = $this->getDoctrine()->getManager();

        $em->remove($mark);
        $em->flush();

        return $this->redirectToRoute('mark');
    }
}
