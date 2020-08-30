<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Student;
use App\Repository\MarkRepository;
use App\Repository\StudentRepository;
use App\Service\MarkHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MarkController extends AbstractController
{
    private $markRepository;
    private $markHelper;
    private $studentRepository;
    private $serializer;
    private $validator;
    private $normalize;

    public function __construct(
        MarkRepository $markRepository,
        MarkHelper $markHelper,
        StudentRepository $studentRepository,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        NormalizerInterface $normalize
    )
    {
        $this->markRepository = $markRepository;
        $this->markHelper = $markHelper;
        $this->studentRepository = $studentRepository;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->normalize = $normalize;
    }

    /**
     * @Route("/api/mark", name="mark", methods={"GET"})
     */
    public function view(): Response
    {
        $marks = $this->markRepository->findAll();
        $response = $this->json($marks,200, [], ['groups' => 'mark:read']);
        $datas = json_decode($response->getContent());
        foreach ($datas as $data) {
            $data->name = $this->markRepository->find($data->id)->getStudent()->getName();
            $data->firstname = $this->markRepository->find($data->id)->getStudent()->getFirstname();
        }

        return $this->render('mark/mark.html.twig', [
            'marks' => $datas
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     * @Route("/api/mark/add", name="mark_add", methods={"GET","POST"})
     */
    public function add(Request $request)
    {
        // On crée le FormBuilder grâce au service form factory
        $form = $this->createFormBuilder(new Mark(), [])
            ->add('student',EntityType::class, array(
                    'class' => Student::class,
                    'choice_label' => function ($student) {
                        return $student ? $student->getFirstname().' '.$student->getName() : '';
                    }
            ))
            ->add('lesson',TextType::class, array('required' => true))
            ->add('value',NumberType::class, array('required' => true))
            ->add('save',SubmitType::class)
            ->getForm()
        ;

        // Si l'utilisateur valide le formulaire, appel de l'API en POST
        if ($request->isMethod('POST')) {
            try {
                // On récupère l'entité mark générée par les données du formulaire
                $idStudent = $request->request->get('form')['student'];
                $mark = $this->markHelper->getMarkFromFormRequest($request);
                $mark->setStudent($this->studentRepository->find($idStudent));

                //On fait un contrôle sur les attributs de l'entité générée
                $errors = $this->validator->validate($mark);
                if (count($errors) > 0) {
                    return $this->json($errors,400);
                }

                // On enregistre l'entité $mark dans la base de données
                $em = $this->getDoctrine()->getManager();
                $em->persist($mark);
                $em->flush();

                // On redirige vers la page d'accueil de la gestion des notes
                return $this->redirectToRoute('mark');

            } catch (NotEncodableValueException $e) {
                return $this->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ],400);
            }
        }

        // Dans le cas d'un formulaire non validé par l'utilisateur
        return $this->render('mark/add.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Request $request
     * @param Mark $mark
     * @return RedirectResponse|Response
     * @Route("/api/mark/update/{id}", name="mark_update", methods={"GET","POST"})
     */
    public function update(Request $request, Mark $mark)
    {
        // On crée le FormBuilder grâce au service form factory
        $form = $this->createFormBuilder($mark, [])
            ->add('student',EntityType::class, array(
                'class' => Student::class,
                'disabled' => true,
                'choice_label' => function ($student) {
                    return $student ? $student->getFirstname().' '.$student->getName() : '';
                }
            ))
            ->add('lesson',TextType::class, array('required' => true))
            ->add('value',NumberType::class, array('required' => true))
            ->add('save',SubmitType::class)
            ->getForm()
        ;

        // Si l'utilisateur valide le formulaire, appel de l'API en POST
        if ($request->isMethod('POST')) {
            // On récupère l'entité mark générée par les données du formulaire
            $newMark = $this->markHelper->getMarkFromFormRequest($request);
            $mark->setLesson($newMark->getLesson());
            $mark->setValue($newMark->getValue());

            //On fait un contrôle sur les attributs de l'entité générée
            $errors = $this->validator->validate($mark);
            if (count($errors) > 0) {
                return $this->json($errors,400);
            }

            // On met à jour l'entité $mark dans la base de données
            $em = $this->getDoctrine()->getManager();
            $em->persist($mark);
            $em->flush();

            // On redirige vers la page d'accueil de la gestion des notes
            return $this->redirectToRoute('mark');
        }

        // Dans le cas d'un formulaire non validé par l'utilisateur
        return $this->render('mark/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @param Mark $mark
     * @return RedirectResponse
     * @Route("/api/mark/delete/{id}", name="mark_delete", methods={"GET","DELETE"})
     */
    public function delete(Mark $mark)
    {
        // On supprime la note
        $em = $this->getDoctrine()->getManager();
        $em->remove($mark);
        $em->flush();

        // On redirige vers la page d'accueil de la gestion des notes
        return $this->redirectToRoute('mark');
    }
}
