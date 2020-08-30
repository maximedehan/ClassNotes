<?php

namespace App\Service;

use App\Entity\Mark;
use App\Entity\Student;
use App\Repository\MarkRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class MarkHelper
{
    private $markRepository;
    private $serializer;

    public function __construct(MarkRepository $markRepository, SerializerInterface $serializer)
    {
        $this->markRepository = $markRepository;
        $this->serializer = $serializer;
    }

    public function getMarksByStudent(Student $student): array
    {
        $marksArray = [];
        $marks = $student->getMarks()->getValues();
        foreach ($marks as $mark) {
            $marksArray[] = [
            'student_id' => $student->getId(),
            'lesson' => $mark->getLesson(),
            'value' => $mark->getValue()
            ];
        }

        return $marksArray;
    }

    public function getAverageByClass(): float
    {
        $marks = $this->markRepository->findAll();
        $average = 0;
        foreach ($marks as $mark) {
            $average = $average + $mark->getValue();
        }

        return round($average/count($marks),2);
    }

    public function getAverageByStudent(Student $student): string
    {
        $marks = $student->getMarks()->getValues();
        $average = 0;
        foreach ($marks as $mark) {
            $marksArray[] = ['lesson' => $mark->getLesson(), 'value' => $mark->getValue()];
            $average = $average + $mark->getValue();
        }

        return count($marks) > 0 ? strval(round($average/count($marks), 2)) : 'Cet élève n\'a aucune note saisie';
    }

    public function getMarkFromFormRequest(Request $request)
    {
        // On récupère les données saisies par l'utilisateur
        $data = $request->request->get('form');
        $data['value'] = floatval($data['value']);
        unset($data['student']);

        // On génère l'entité $mark
        return $this->serializer->deserialize(json_encode($data),Mark::class,'json');
    }
}
